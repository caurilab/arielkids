<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;

interface PaymentGateway
{
    public function method(): PaymentMethod;

    /**
     * Démarre le paiement pour une commande.
     *
     * @return string|null URL de redirection vers le guichet de paiement,
     *                     ou null si aucune redirection (ex. paiement à la livraison).
     */
    public function initiate(Order $order): ?string;

    /**
     * Vérifie le statut réel d'une transaction auprès du prestataire.
     * Ne jamais se fier au seul callback/webhook.
     */
    public function verify(string $transactionId): PaymentStatus;
}
