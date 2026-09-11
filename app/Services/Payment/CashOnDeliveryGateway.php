<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;

/**
 * Paiement à la livraison : la commande part en préparation,
 * le paiement reste en attente jusqu'à la remise du colis.
 */
class CashOnDeliveryGateway implements PaymentGateway
{
    public function method(): PaymentMethod
    {
        return PaymentMethod::Livraison;
    }

    public function initiate(Order $order): ?string
    {
        $order->update([
            'payment_status' => PaymentStatus::EnAttente,
            'status' => OrderStatus::EnPreparation,
        ]);

        return null;
    }

    public function verify(string $transactionId): PaymentStatus
    {
        // Pas de prestataire externe : le statut est géré en back-office.
        return PaymentStatus::EnAttente;
    }
}
