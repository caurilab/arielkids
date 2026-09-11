<?php

namespace App\Http\Controllers\Shop;

use App\Enums\Audience;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __invoke(Request $request, Category $category): Response
    {
        abort_unless($category->is_active, 404);

        $filters = $request->validate([
            'audience' => ['nullable', 'string'],
            'min' => ['nullable', 'integer', 'min:0'],
            'max' => ['nullable', 'integer', 'min:0'],
            'disponible' => ['nullable', 'boolean'],
            'tri' => ['nullable', 'string', 'in:nouveaute,prix_asc,prix_desc,nom'],
        ]);

        $categoryIds = $category->descendantIds();

        $query = Product::query()
            ->active()
            ->with('media')
            ->whereIn('category_id', $categoryIds);

        if (! empty($filters['audience'])) {
            $audience = Audience::tryFrom($filters['audience']);
            if ($audience) {
                $query->where('audience', $audience);
            }
        }

        if (! empty($filters['min'])) {
            $query->where('price', '>=', $filters['min']);
        }

        if (! empty($filters['max'])) {
            $query->where('price', '<=', $filters['max']);
        }

        if (! empty($filters['disponible'])) {
            $query->inStock();
        }

        match ($filters['tri'] ?? null) {
            'prix_asc' => $query->orderBy('price'),
            'prix_desc' => $query->orderByDesc('price'),
            'nom' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $category->load(['parent', 'children' => fn ($q) => $q->where('is_active', true)->orderBy('position')]);

        return Inertia::render('Shop/Category', [
            'category' => $category,
            'products' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'audience' => $filters['audience'] ?? null,
                'min' => $filters['min'] ?? null,
                'max' => $filters['max'] ?? null,
                'disponible' => (bool) ($filters['disponible'] ?? false),
                'tri' => $filters['tri'] ?? 'nouveaute',
            ],
            'audiences' => collect(Audience::cases())->map(fn (Audience $a) => [
                'value' => $a->value,
                'label' => $a->label(),
            ]),
        ]);
    }
}
