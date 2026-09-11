<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __invoke(Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load(['media', 'category.parent']);

        $related = Product::query()
            ->active()
            ->inStock()
            ->where('audience', $product->audience)
            ->where('id', '!=', $product->id)
            ->with('media')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return Inertia::render('Shop/Product', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
