<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Filles' => ['Vêtements', 'Chaussures', 'Sacs', 'Accessoires'],
            'Garçons' => ['Vêtements', 'Chaussures', 'Sacs', 'Accessoires'],
            'Bébé (0–2 ans)' => ['Vêtements', 'Chaussures', 'Accessoires'],
            'Femmes' => ['Vêtements', 'Chaussures', 'Sacs', 'Montres', 'Accessoires'],
            'Hommes' => ['Vêtements', 'Chaussures', 'Sacs', 'Montres', 'Accessoires'],
        ];

        $position = 0;

        foreach ($tree as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'parent_id' => null,
                    'position' => $position++,
                    'is_active' => true,
                ],
            );

            foreach ($children as $childPosition => $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($parent->name.' '.$childName)],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'position' => $childPosition,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
