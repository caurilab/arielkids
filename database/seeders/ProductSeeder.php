<?php

namespace Database\Seeders;

use App\Enums\Audience;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $manifest = json_decode(
            file_get_contents(database_path('seeders/data/products.json')),
            true,
        );

        foreach ($manifest as $item) {
            $category = Category::where(
                'slug',
                Str::slug($item['parent'].' '.$item['child']),
            )->firstOrFail();

            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => "Sélection Ariel Kid's : {$item['name']}. Matières de qualité, finitions soignées et confort au quotidien. Stock limité — livraison rapide à Abidjan et dans toute la Côte d'Ivoire.",
                    'price' => $item['price'],
                    'audience' => Audience::from($item['audience']),
                    'sku' => 'AK-'.strtoupper(Str::random(6)),
                    'stock' => $item['stock'],
                    'is_active' => true,
                    'is_featured' => $item['featured'],
                ],
            );

            Media::updateOrCreate(
                ['product_id' => $product->id, 'path' => "products/{$item['slug']}.jpg"],
                ['alt' => $item['name'], 'position' => 0],
            );
        }
    }
}
