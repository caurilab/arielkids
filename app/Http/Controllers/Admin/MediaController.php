<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Media::with('product:id,name')->latest();

        if ($search = $request->string('q')->trim()->toString()) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        return Inertia::render('Admin/Media/Index', [
            'media' => $query->paginate(24)->withQueryString(),
            'filters' => ['q' => $search ?: null],
        ]);
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $data = $request->validate([
            'alt' => ['nullable', 'string', 'max:190'],
        ]);

        $media->update($data);

        return back()->with('success', 'Image mise à jour.');
    }

    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return back()->with('success', 'Image supprimée.');
    }
}
