<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Categories/Index', [
            'tree' => Category::whereNull('parent_id')
                ->orderBy('position')
                ->with(['children' => fn ($q) => $q->orderBy('position')->withCount('products')])
                ->withCount('products')
                ->get(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validatedData());

        return back()->with('success', 'Catégorie créée.');
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validatedData();

        // Une catégorie ne peut pas devenir sa propre descendante.
        if (($data['parent_id'] ?? null) === $category->id) {
            return back()->with('error', 'Une catégorie ne peut pas être son propre parent.');
        }

        $category->update($data);

        return back()->with('success', 'Catégorie mise à jour.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        foreach ($data['ordered_ids'] as $position => $id) {
            Category::whereKey($id)->update(['position' => $position]);
        }

        return back();
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Impossible : la catégorie contient des produits.');
        }

        $category->children()->update(['parent_id' => $category->parent_id]);
        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}
