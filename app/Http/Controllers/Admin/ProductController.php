<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Audience;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::query()
            ->with(['media', 'category'])
            ->withCount('media')
            ->latest();

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%"));
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => $query->paginate(15)->withQueryString(),
            'filters' => ['q' => $search ?: null],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            ...$this->formOptions(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request): Product {
            $product = Product::create($request->validatedData());

            $this->syncImages($request, $product);

            return $product;
        });

        return to_route('admin.products.edit', $product)
            ->with('success', 'Produit créé.');
    }

    public function edit(Product $product): Response
    {
        $product->load('media');

        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            ...$this->formOptions(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product): void {
            $product->update($request->validatedData());

            $this->syncImages($request, $product);
        });

        return back()->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return to_route('admin.products.index')->with('success', 'Produit supprimé.');
    }

    /** Gère l'upload multi-images et les suppressions demandées. */
    private function syncImages(ProductRequest $request, Product $product): void
    {
        foreach ($request->input('delete_media', []) as $mediaId) {
            $product->media()->whereKey($mediaId)->get()->each->delete();
        }

        $position = (int) $product->media()->max('position');

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('products', 'public');

            $product->media()->create([
                'path' => $path,
                'alt' => $product->name,
                'position' => ++$position,
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'categories' => Category::whereNotNull('parent_id')
                ->where('is_active', true)
                ->orderBy('position')
                ->with('parent:id,name')
                ->get(['id', 'parent_id', 'name']),
            'audiences' => collect(Audience::cases())->map(fn (Audience $a) => [
                'value' => $a->value,
                'label' => $a->label(),
            ]),
        ];
    }
}
