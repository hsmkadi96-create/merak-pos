<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class DahabiaPaymentController extends Controller
{
    /**
     * Initialiser un paiement DAHABIA.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'order_id' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $config = Config::get('app.dahabia');

        if (!$config['enabled'] || empty($config['api_key'])) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement DAHABIA non configuré. Veuillez configurer les clés API dans le fichier .env.',
                'sandbox' => true,
            ], 400);
        }

        try {
            $response = Http::timeout(15)->post($config['api_url'] . '/v1/payments/create', [
                'merchant_id' => $config['merchant_id'],
                'api_key' => $config['api_key'],
                'amount' => $request->amount,
                'currency' => 'DZD',
                'order_id' => $request->order_id,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'description' => $request->description ?? 'Paiement MERAK POS',
                'callback_url' => route('payment.dahabia.callback'),
                'return_url' => route('payment.dahabia.return'),
            ], [
                'Authorization' => 'Bearer ' . $config['api_secret'],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('DAHABIA Payment initiated', [
                    'order_id' => $request->order_id,
                    'amount' => $request->amount,
                    'response' => $data,
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $data['payment_url'] ?? null,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'sandbox' => str_contains($config['api_url'], 'sandbox'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur DAHABIA : ' . ($response->json()['message'] ?? 'Erreur inconnue'),
            ], 400);

        } catch (\Exception $e) {
            Log::error('DAHABIA Payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion DAHABIA : ' . $e->getMessage(),
                'sandbox' => true,
            ], 500);
        }
    }

    /**
     * Callback DAHABIA.
     */
    public function callback(Request $request)
    {
        $payload = $request->all();

        Log::info('DAHABIA Callback received', $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Page de retour après paiement DAHABIA.
     */
    public function return(Request $request)
    {
        $status = $request->query('status', 'pending');
        $transactionId = $request->query('transaction_id');

        return view('payment.dahabia-return', compact('status', 'transactionId'));
    }
}
