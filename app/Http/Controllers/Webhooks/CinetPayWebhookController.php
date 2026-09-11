<?php

namespace App\Http\Controllers\Webhooks;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\CinetPayGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;

/**
 * Notification asynchrone CinetPay (notify_url).
 *
 * Le callback seul ne fait JAMAIS foi : on extrait l'identifiant de
 * transaction puis on interroge l'API CinetPay pour obtenir le statut réel.
 */
class CinetPayWebhookController extends Controller
{
    public function __invoke(Request $request, CinetPayGateway $cinetpay): HttpResponse
    {
        $transactionId = $request->input('cpm_trans_id')
            ?? $request->input('merchant_transaction_id')
            ?? $request->input('transaction_id');

        if (! is_string($transactionId) || $transactionId === '') {
            Log::warning('Webhook CinetPay sans identifiant de transaction', $request->all());

            return response('missing transaction id', 400);
        }

        $order = Order::where('reference', $transactionId)
            ->orWhere('cinetpay_transaction_id', $transactionId)
            ->first();

        if (! $order) {
            Log::warning('Webhook CinetPay : commande introuvable', ['transaction' => $transactionId]);

            return response('unknown transaction', 404);
        }

        // Idempotence : une commande déjà soldée n'est pas retraitée.
        if ($order->payment_status === PaymentStatus::Paye) {
            return response('already processed', 200);
        }

        try {
            $status = $cinetpay->verify($order->reference);
        } catch (\Throwable $e) {
            Log::error('Webhook CinetPay : vérification impossible', [
                'order' => $order->reference,
                'error' => $e->getMessage(),
            ]);

            return response('verification failed', 502);
        }

        $order->update([
            'payment_status' => $status,
            'status' => match ($status) {
                PaymentStatus::Paye => OrderStatus::Payee,
                PaymentStatus::Echoue => OrderStatus::Annulee,
                default => $order->status,
            },
        ]);

        return response('ok', 200);
    }
}
