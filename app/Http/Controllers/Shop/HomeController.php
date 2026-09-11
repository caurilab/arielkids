<?php

namespace App\Http\Controllers\Shop;

use App\Enums\Audience;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Instagram\InstagramService;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(InstagramService $instagram): Response
    {
        return Inertia::render('Shop/Home', [
            'featured' => $this->kidsFirst(
                fn (Builder $q) => $q->where('is_featured', true),
            ),
            'newArrivals' => $this->kidsFirst(
                fn (Builder $q) => $q->latest(),
            ),
            'audiences' => $this->audienceCards(),
            'instagramPosts' => fn () => $instagram->feed(6),
        ]);
    }

    /**
     * « Enfants d'abord » : produits enfants prioritaires, bascule
     * automatique vers les adultes si le stock enfant est insuffisant.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Product>
     */
    private function kidsFirst(callable $scope, int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        $base = fn () => Product::query()
            ->active()
            ->inStock()
            ->with('media')
            ->tap($scope);

        $children = (clone $base())->children()->limit($limit)->get();

        if ($children->count() < $limit) {
            $adults = (clone $base())
                ->adults()
                ->limit($limit - $children->count())
                ->get();

            return $children->concat($adults)->values();
        }

        return $children;
    }

    /** @return list<array{label:string,audience:string,slug:string}> */
    private function audienceCards(): array
    {
        $cards = [
            ['label' => 'Filles', 'audience' => Audience::EnfantFille->value, 'parent' => 'Filles'],
            ['label' => 'Garçons', 'audience' => Audience::EnfantGarcon->value, 'parent' => 'Garçons'],
            ['label' => 'Bébé', 'audience' => Audience::Bebe->value, 'parent' => 'Bébé (0–2 ans)'],
            ['label' => 'Femmes', 'audience' => Audience::Femme->value, 'parent' => 'Femmes'],
            ['label' => 'Hommes', 'audience' => Audience::Homme->value, 'parent' => 'Hommes'],
        ];

        return collect($cards)
            ->map(function (array $card) {
                $category = Category::where('slug', \Illuminate\Support\Str::slug($card['parent']))->first();
                $product = Product::active()->inStock()
                    ->where('audience', $card['audience'])
                    ->with('media')
                    ->inRandomOrder()
                    ->first();

                return [
                    'label' => $card['label'],
                    'audience' => $card['audience'],
                    'slug' => $category?->slug,
                    'image' => $product?->firstImageUrl(),
                ];
            })
            ->all();
    }
}
