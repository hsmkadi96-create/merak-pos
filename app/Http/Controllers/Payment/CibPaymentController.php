<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class CibPaymentController extends Controller
{
    /**
     * Initialiser un paiement CIB.
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

        $config = Config::get('app.cib');

        if (!$config['enabled'] || empty($config['api_key'])) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement CIB non configuré. Veuillez configurer les clés API dans le fichier .env.',
                'sandbox' => true,
            ], 400);
        }

        try {
            $response = Http::timeout(15)->post($config['api_url'] . '/v1/payment/initiate', [
                'merchant_id' => $config['merchant_id'],
                'api_key' => $config['api_key'],
                'amount' => $request->amount,
                'currency' => 'DZD',
                'order_id' => $request->order_id,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'description' => $request->description ?? 'Paiement MERAK POS',
                'callback_url' => route('payment.cib.callback'),
                'return_url' => route('payment.cib.return'),
            ], [
                'Authorization' => 'Bearer ' . $config['api_secret'],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('CIB Payment initiated', [
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
                'message' => 'Erreur CIB : ' . ($response->json()['message'] ?? 'Erreur inconnue'),
            ], 400);

        } catch (\Exception $e) {
            Log::error('CIB Payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion CIB : ' . $e->getMessage(),
                'sandbox' => true,
            ], 500);
        }
    }

    /**
     * Callback CIB (webhook de notification).
     */
    public function callback(Request $request)
    {
        $signature = $request->header('X-CIB-Signature');
        $payload = $request->all();

        Log::info('CIB Callback received', $payload);

        // Vérification de la signature (à implémenter avec la clé secrète CIB)
        // $isValid = hash_hmac('sha256', json_encode($payload), config('app.cib.api_secret')) === $signature;

        // Mise à jour du paiement dans la base de données
        // PaymentSale::where('transaction_id', $payload['transaction_id'])->update([...]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Page de retour après paiement CIB.
     */
    public function return(Request $request)
    {
        $status = $request->query('status', 'pending');
        $transactionId = $request->query('transaction_id');

        return view('payment.cib-return', compact('status', 'transactionId'));
    }
}
