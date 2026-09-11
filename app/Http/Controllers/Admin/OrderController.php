<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::with('customer')->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $query->paginate(15)->withQueryString(),
            'filters' => ['status' => $status ?: null],
            'statuses' => collect(OrderStatus::cases())->map(fn (OrderStatus $s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['customer', 'items']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'statuses' => collect(OrderStatus::cases())->map(fn (OrderStatus $s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'paymentStatuses' => collect(PaymentStatus::cases())->map(fn (PaymentStatus $s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['sometimes', Rule::enum(OrderStatus::class)],
            'payment_status' => ['sometimes', Rule::enum(PaymentStatus::class)],
        ]);

        $order->update($data);

        return back()->with('success', 'Commande mise à jour.');
    }
}
