<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/**
 * BannerController - Menangani banner untuk admin
 */
class BannerController extends Controller
{
    /**
     * List all banners
     */
    public function index()
    {
        $banners = Banner::orderBy('order')->get();

        return Inertia::render('Admin/Banner/Index', [
            'banners' => $banners,
        ]);
    }

    /**
     * Store new banner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $validated['title'] ?? null,
            'image' => $imagePath,
            'link' => $validated['link'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    /**
     * Update banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        } else {
            unset($validated['image']);
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui.');
    }

    /**
     * Delete banner
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return back()->with('success', 'Status banner berhasil diperbarui.');
    }
}
