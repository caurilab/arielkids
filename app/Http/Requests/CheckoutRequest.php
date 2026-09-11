<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'min:8', 'max:20'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['required', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Votre nom est requis.',
            'phone.required' => 'Votre numéro de téléphone est requis.',
            'phone.min' => 'Le numéro de téléphone semble trop court.',
            'address.required' => 'Votre adresse de livraison est requise.',
            'payment_method.required' => 'Choisissez un mode de paiement.',
        ];
    }
}
