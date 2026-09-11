<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * CinetPay (Orange Money, MTN, Moov, Wave, cartes) — API v1.
 *
 * Flux : POST /v1/oauth/login → access_token
 *        POST /v1/payment     → payment_token + payment_url (guichet)
 *        GET  /v1/payment/{merchant_transaction_id} → statut canonique
 *
 * Les noms de champs suivent la documentation officielle CinetPay.
 * À revalider avec un compte marchand réel avant la mise en production.
 */
class CinetPayGateway implements PaymentGateway
{
    public function method(): PaymentMethod
    {
        return PaymentMethod::MobileMoney;
    }

    public function initiate(Order $order): ?string
    {
        $this->ensureConfigured();

        $order->loadMissing('customer');

        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->post($this->url('/v1/payment'), [
                'currency' => config('cinetpay.currency'),
                'merchant_transaction_id' => $order->reference,
                'amount' => $order->total,
                'lang' => config('cinetpay.lang'),
                'designation' => "Commande {$order->reference} — Ariel Kid's",
                'client_email' => $order->customer->email,
                'client_first_name' => $order->customer->name,
                'client_last_name' => $order->customer->name,
                'client_phone_number' => $order->customer->phone,
                'notify_url' => config('cinetpay.notify_url') ?: route('webhooks.cinetpay'),
                'success_url' => config('cinetpay.return_url')
                    ?: route('checkout.confirmation', $order->reference),
                'failed_url' => route('checkout.index'),
                'channel' => 'PUSH',
                'direct_pay' => false,
            ])
            ->throw()
            ->json();

        $paymentUrl = $response['payment_url']
            ?? $response['data']['payment_url']
            ?? null;

        if (! is_string($paymentUrl) || $paymentUrl === '') {
            throw new RuntimeException(
                'CinetPay n\'a pas retourné de payment_url : '.json_encode($response),
            );
        }

        return $paymentUrl;
    }

    public function verify(string $transactionId): PaymentStatus
    {
        $this->ensureConfigured();

        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->get($this->url("/v1/payment/{$transactionId}"))
            ->throw()
            ->json();

        $status = strtoupper((string) ($response['status'] ?? $response['data']['status'] ?? ''));

        return match ($status) {
            'SUCCESS', 'ACCEPTED', '00' => PaymentStatus::Paye,
            'REFUSED', 'FAILED', 'CANCELED', 'EXPIRED' => PaymentStatus::Echoue,
            default => PaymentStatus::EnAttente,
        };
    }

    private function accessToken(): string
    {
        return Cache::remember('cinetpay.access_token', now()->addMinutes(50), function (): string {
            $response = Http::acceptJson()
                ->post($this->url('/v1/oauth/login'), [
                    'api_key' => config('cinetpay.api_key'),
                    'api_password' => config('cinetpay.secret_key'),
                ])
                ->throw()
                ->json();

            $token = $response['access_token'] ?? $response['data']['access_token'] ?? null;

            if (! is_string($token) || $token === '') {
                throw new RuntimeException('Authentification CinetPay impossible.');
            }

            return $token;
        });
    }

    private function url(string $path): string
    {
        return rtrim((string) config('cinetpay.base_url'), '/').$path;
    }

    private function ensureConfigured(): void
    {
        if (! config('cinetpay.api_key') || ! config('cinetpay.secret_key')) {
            throw new RuntimeException(
                'CinetPay n\'est pas configuré (CINETPAY_API_KEY / CINETPAY_SECRET_KEY manquants).',
            );
        }
    }
}
