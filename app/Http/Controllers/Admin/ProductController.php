<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * ProductController - Menangani produk untuk admin
 */
class ProductController extends Controller
{
    /**
     * List all products
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Product::with(['category', 'admin']);

        // Admin biasa hanya melihat produknya sendiri
        if (!$isSuperAdmin) {
            $query->where('admin_id', $user->id);
        }

        // Search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Filter by category
        if ($categoryId = $request->get('category')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::active()->ordered()->get();

        return Inertia::render('Admin/Product/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'active']),
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();

        return Inertia::render('Admin/Product/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'verified_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'main_image' => 'required|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Generate slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Store main image
        $mainImagePath = $request->file('main_image')->store('products', 'public');

        // Create product
        $product = Product::create([
            'admin_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'price' => $validated['price'],
            'verified_price' => $validated['verified_price'],
            'stock' => $validated['stock'],
            'main_image' => $mainImagePath,
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        // Store additional images (max 4)
        if ($request->hasFile('images')) {
            $images = array_slice($request->file('images'), 0, 4);
            foreach ($images as $index => $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show edit form
     */
    public function edit(Product $product)
    {
        $user = Auth::user();

        // Check ownership
        if (!$user->isSuperAdmin() && $product->admin_id !== $user->id) {
            abort(403);
        }

        $product->load('images');
        $categories = Category::active()->ordered()->get();

        return Inertia::render('Admin/Product/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        // Check ownership
        if (!$user->isSuperAdmin() && $product->admin_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'verified_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'main_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $product->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $validated['slug'] = $slug;
        }

        // Update main image if provided
        if ($request->hasFile('main_image')) {
            // Delete old image
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        } else {
            unset($validated['main_image']);
        }

        // Update product
        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $product->slug,
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'price' => $validated['price'],
            'verified_price' => $validated['verified_price'],
            'stock' => $validated['stock'],
            'main_image' => $validated['main_image'] ?? $product->main_image,
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        // Handle additional images
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Store new images
            $images = array_slice($request->file('images'), 0, 4);
            foreach ($images as $index => $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();

        // Check ownership
        if (!$user->isSuperAdmin() && $product->admin_id !== $user->id) {
            abort(403);
        }

        // Delete images
        if ($product->main_image) {
            Storage::disk('public')->delete($product->main_image);
        }
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Delete single additional image
     */
    public function deleteImage(ProductImage $image)
    {
        $user = Auth::user();
        $product = $image->product;

        // Check ownership
        if (!$user->isSuperAdmin() && $product->admin_id !== $user->id) {
            abort(403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Gambar berhasil dihapus.');
    }
}
