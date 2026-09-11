<?php

namespace App\Services\Cart;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Panier persisté en session. Chaque ligne fige le nom, le prix
 * et l'image du produit au moment de l'ajout.
 */
class CartService
{
    private const SESSION_KEY = 'cart.items';

    /** @return Collection<string, array{product_id:int,name:string,slug:string,price:int,image:?string,quantity:int,stock:int}> */
    public function items(): Collection
    {
        return collect(Session::get(self::SESSION_KEY, []));
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->items();
        $key = (string) $product->id;

        $existing = $items->get($key);
        $newQuantity = min(($existing['quantity'] ?? 0) + $quantity, $product->stock);

        $items->put($key, [
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'image' => $product->firstImageUrl(),
            'quantity' => $newQuantity,
            'stock' => $product->stock,
        ]);

        Session::put(self::SESSION_KEY, $items->all());
    }

    public function update(int $productId, int $quantity): void
    {
        $items = $this->items();
        $key = (string) $productId;

        if (! $items->has($key)) {
            return;
        }

        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        $item = $items->get($key);
        $item['quantity'] = min($quantity, $item['stock']);
        $items->put($key, $item);

        Session::put(self::SESSION_KEY, $items->all());
    }

    public function remove(int $productId): void
    {
        Session::put(self::SESSION_KEY, $this->items()->forget((string) $productId)->all());
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function total(): int
    {
        return (int) $this->items()->sum(
            fn (array $item) => $item['price'] * $item['quantity'],
        );
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items()->values()->all(),
            'count' => $this->count(),
            'total' => $this->total(),
        ];
    }
}
