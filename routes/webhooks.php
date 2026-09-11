<?php

use App\Http\Controllers\Webhooks\CinetPayWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/cinetpay', CinetPayWebhookController::class)
    ->name('webhooks.cinetpay');
