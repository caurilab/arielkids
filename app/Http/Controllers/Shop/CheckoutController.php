<?php

namespace App\Http\Controllers\Shop;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PaymentManager $payments,
    ) {}

    public function index(): Response|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return to_route('cart.index')->with('error', 'Votre panier est vide.');
        }

        return Inertia::render('Shop/Checkout', [
            'paymentMethods' => collect(PaymentMethod::cases())->map(fn (PaymentMethod $m) => [
                'value' => $m->value,
                'label' => $m->label(),
            ]),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return to_route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $data = $request->validated();

        $order = DB::transaction(function () use ($data): Order {
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
            ]);

            $order = $customer->orders()->create([
                'payment_method' => $data['payment_method'],
                'total' => $this->cart->total(),
            ]);

            foreach ($this->cart->items() as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (! $product || ! $product->is_active || $product->stock < $item['quantity']) {
                    throw new RuntimeException("Stock insuffisant pour {$item['name']}.");
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        $gateway = $this->payments->gateway($order->payment_method);
        $redirectUrl = $gateway->initiate($order);

        $this->cart->clear();

        if ($redirectUrl !== null) {
            return Inertia::location($redirectUrl);
        }

        return to_route('checkout.confirmation', $order->reference)
            ->with('success', 'Votre commande a bien été enregistrée.');
    }

    public function confirmation(string $reference): Response
    {
        $order = Order::where('reference', $reference)
            ->with(['customer', 'items'])
            ->firstOrFail();

        return Inertia::render('Shop/Confirmation', [
            'order' => $order,
        ]);
    }
}
