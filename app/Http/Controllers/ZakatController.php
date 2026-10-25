<?php

namespace App\Http\Controllers;

use App\Models\ZakatReport;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ZakatController extends Controller
{
    /**
     * Dashboard Zakat - calcul et rapports.
     */
    public function index(Request $request)
    {
        $tenantId = session('current_tenant_id');
        $tenant = Tenant::find($tenantId);

        $reports = ZakatReport::where('tenant_id', $tenantId)
            ->orderBy('period', 'desc')
            ->paginate(10);

        // Calcul automatique du zakat actuel
        $currentCalculation = null;
        if ($tenantId) {
            $totalStock = Product::where('tenant_id', $tenantId)->sum('price');
            $nisabThreshold = 50000; // Seuil nisab par défaut en DZD (configurable)

            $currentCalculation = ZakatReport::calculate([
                'nisab_threshold' => $nisabThreshold,
                'total_stock_value' => $totalStock,
                'total_cash' => 0, // À venir depuis comptabilité
                'total_receivables' => 0,
            ]);
        }

        return view('zakat.index', compact('reports', 'currentCalculation', 'tenant'));
    }

    /**
     * Créer un nouveau rapport Zakat.
     */
    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required|string|max:100',
            'nisab_threshold' => 'required|numeric|min:0',
            'total_stock_value' => 'required|numeric|min:0',
            'total_cash' => 'nullable|numeric|min:0',
            'total_receivables' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $tenantId = session('current_tenant_id');

        $calculation = ZakatReport::calculate([
            'nisab_threshold' => $request->nisab_threshold,
            'total_stock_value' => $request->total_stock_value,
            'total_cash' => $request->total_cash ?? 0,
            'total_receivables' => $request->total_receivables ?? 0,
        ]);

        $report = ZakatReport::create([
            'tenant_id' => $tenantId,
            'period' => $request->period,
            'nisab_threshold' => $request->nisab_threshold,
            'total_stock_value' => $request->total_stock_value,
            'total_cash' => $request->total_cash ?? 0,
            'total_receivables' => $request->total_receivables ?? 0,
            'zakat_base' => $calculation['zakat_base'],
            'zakat_amount' => $calculation['zakat_amount'],
            'notes' => $request->notes,
            'is_paid' => false,
        ]);

        return redirect()->route('zakat.index')->with('success', 'Rapport Zakat créé avec succès.');
    }

    /**
     * Marquer un rapport Zakat comme payé.
     */
    public function markPaid(ZakatReport $zakatReport)
    {
        $zakatReport->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Le Zakat a été marqué comme payé.');
    }

    /**
     * Export du rapport Zakat en PDF (via DomPDF existant).
     */
    public function pdf(ZakatReport $zakatReport)
    {
        $pdf = \PDF::loadView('zakat.pdf', compact('zakatReport'));
        return $pdf->download('zakat-rapport-' . $zakatReport->period . '.pdf');
    }
}
