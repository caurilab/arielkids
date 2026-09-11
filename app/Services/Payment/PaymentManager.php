<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;
use InvalidArgumentException;

class PaymentManager
{
    public function gateway(PaymentMethod $method): PaymentGateway
    {
        return match ($method) {
            PaymentMethod::MobileMoney => app(CinetPayGateway::class),
            PaymentMethod::Livraison => app(CashOnDeliveryGateway::class),
            default => throw new InvalidArgumentException("Mode de paiement non géré : {$method->value}"),
        };
    }
}
