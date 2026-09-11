<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Réessayez dans quelques instants.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Ces identifiants sont incorrects.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function throttleKey(): string
    {
        return mb_strtolower((string) $this->input('email')).'|'.$this->ip();
    }
}
