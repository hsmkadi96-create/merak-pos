<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Dashboard restaurant - vue des tables.
     */
    public function index(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $tables = RestaurantTable::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $stats = [
            'total_tables' => $tables->count(),
            'free_tables' => $tables->where('status', 'free')->count(),
            'occupied_tables' => $tables->where('status', 'occupied')->count(),
            'reserved_tables' => $tables->where('status', 'reserved')->count(),
            'active_orders' => RestaurantOrder::where('tenant_id', $tenantId)
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->count(),
        ];

        $waiters = User::where('tenant_id', $tenantId)->get();

        return view('restaurant.index', compact('tables', 'stats', 'waiters'));
    }

    /**
     * Changer le statut d'une table.
     */
    public function updateTableStatus(Request $request, RestaurantTable $table)
    {
        $request->validate([
            'status' => 'required|in:free,occupied,reserved,cleaning,maintenance',
        ]);

        $table->update(['status' => $request->status]);

        if ($request->status === 'free') {
            $table->update(['current_order_id' => null]);
        }

        return response()->json([
            'success' => true,
            'table' => $table->fresh(),
        ]);
    }

    /**
     * Créer une commande restaurant.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'waiter_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $tenantId = session('current_tenant_id');
        $table = RestaurantTable::findOrFail($request->table_id);

        $order = RestaurantOrder::create([
            'tenant_id' => $tenantId,
            'table_id' => $request->table_id,
            'waiter_id' => $request->waiter_id,
            'order_number' => 'REST-' . now()->format('YmdHis') . rand(100, 999),
            'status' => 'pending',
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $unitPrice = $product->price ?? 0;
            $totalPrice = $unitPrice * $item['quantity'];
            $subtotal += $totalPrice;

            RestaurantOrderItem::create([
                'restaurant_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'kitchen_status' => 'pending',
                'special_instructions' => $item['special_instructions'] ?? null,
            ]);
        }

        $tax = $subtotal * 0.07; // TVA 7% configurable
        $total = $subtotal + $tax;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        // Marquer la table comme occupée
        $table->update(['status' => 'occupied', 'current_order_id' => $order->id]);

        return response()->json([
            'success' => true,
            'order' => $order->load('items.product'),
        ]);
    }

    /**
     * Mettre à jour le statut cuisine d'un article.
     */
    public function updateKitchenStatus(Request $request, RestaurantOrderItem $item)
    {
        $request->validate([
            'kitchen_status' => 'required|in:pending,in_progress,ready,served',
        ]);

        $item->update(['kitchen_status' => $request->kitchen_status]);

        return response()->json(['success' => true]);
    }

    /**
     * Clôturer une commande (paiement).
     */
    public function closeOrder(Request $request, RestaurantOrder $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,cib,dahabia,card',
        ]);

        $order->update(['status' => 'paid']);

        // Libérer la table
        $table = $order->table;
        $table->update(['status' => 'free', 'current_order_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Commande clôturée et table libérée.',
        ]);
    }
}
