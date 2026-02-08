<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSliderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.view'), 403);

        $sliders = HeroSlider::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.hero-sliders.index', compact('sliders'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        return view('admin.hero-sliders.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:2000'],
            'link' => ['nullable', 'url', 'max:2048'],
            'badge_text' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $path = $request->file('image')->store('sliders', 'public');

        HeroSlider::create([
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'link' => $validated['link'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'sort_order' => (int) $validated['sort_order'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'image' => $path,
        ]);

        return redirect()->route('admin.hero-sliders.index')->with('success', 'Hero slide created successfully.');
    }

    public function edit(Request $request, HeroSlider $hero_slider)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $slider = $hero_slider;
        return view('admin.hero-sliders.edit', compact('slider'));
    }

    public function update(Request $request, HeroSlider $hero_slider)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:2000'],
            'link' => ['nullable', 'url', 'max:2048'],
            'badge_text' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $oldImage = $hero_slider->image;
        $newPath = null;
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('sliders', 'public');
        }

        $hero_slider->update([
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'link' => $validated['link'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'sort_order' => (int) $validated['sort_order'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'image' => $newPath ?: $hero_slider->image,
        ]);

        if ($newPath && $oldImage && $oldImage !== $newPath) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.hero-sliders.index')->with('success', 'Hero slide updated successfully.');
    }

    public function destroy(Request $request, HeroSlider $hero_slider)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $image = $hero_slider->image;
        $hero_slider->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.hero-sliders.index')->with('success', 'Hero slide deleted successfully.');
    }

    public function toggleStatus(Request $request, HeroSlider $hero_slider)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $hero_slider->is_active = !$hero_slider->is_active;
        $hero_slider->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $hero_slider->is_active,
        ]);
    }
}
