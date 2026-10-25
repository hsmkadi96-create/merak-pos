<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class ChifaaPaymentController extends Controller
{
    /**
     * Initialiser un paiement Carte Chifaa (Tiers Payant / Pharmacie).
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'order_id' => 'required|string',
            'patient_id' => 'nullable|string',
            'prescription_number' => 'nullable|string',
            'pharmacy_id' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $config = Config::get('app.chifaa');

        if (!$config['enabled'] || empty($config['api_key'])) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement Carte Chifaa non configuré. Veuillez configurer les clés API dans le fichier .env.',
                'sandbox' => true,
            ], 400);
        }

        try {
            $response = Http::timeout(15)->post($config['api_url'] . '/v1/tiers-payant/authorize', [
                'merchant_id' => $config['merchant_id'],
                'api_key' => $config['api_key'],
                'amount' => $request->amount,
                'currency' => 'DZD',
                'order_id' => $request->order_id,
                'patient_id' => $request->patient_id,
                'prescription_number' => $request->prescription_number,
                'pharmacy_id' => $request->pharmacy_id,
                'description' => $request->description ?? 'Paiement Carte Chifaa - MERAK POS',
                'callback_url' => route('payment.chifaa.callback'),
                'return_url' => route('payment.chifaa.return'),
            ], [
                'Authorization' => 'Bearer ' . $config['api_secret'],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Chifaa Payment initiated', [
                    'order_id' => $request->order_id,
                    'amount' => $request->amount,
                    'patient_id' => $request->patient_id,
                    'response' => $data,
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $data['payment_url'] ?? null,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'authorization_code' => $data['authorization_code'] ?? null,
                    'sandbox' => str_contains($config['api_url'], 'sandbox'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur Chifaa : ' . ($response->json()['message'] ?? 'Erreur inconnue'),
            ], 400);

        } catch (\Exception $e) {
            Log::error('Chifaa Payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion Chifaa : ' . $e->getMessage(),
                'sandbox' => true,
            ], 500);
        }
    }

    /**
     * Callback Chifaa.
     */
    public function callback(Request $request)
    {
        $payload = $request->all();

        Log::info('Chifaa Callback received', $payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Page de retour après paiement Chifaa.
     */
    public function return(Request $request)
    {
        $status = $request->query('status', 'pending');
        $transactionId = $request->query('transaction_id');

        return view('payment.chifaa-return', compact('status', 'transactionId'));
    }
}
