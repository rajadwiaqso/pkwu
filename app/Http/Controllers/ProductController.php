<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * ProductController - Menangani halaman produk untuk guest/buyer
 */
class ProductController extends Controller
{
    /**
     * Show product detail
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'admin', 'images'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Increment view count
        $product->incrementViews();

        // Get related products
        $relatedProducts = Product::with(['category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return Inertia::render('Product/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * All products page
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'admin'])
            ->active()
            ->inStock();

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sold_count', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Product/Index', [
            'products' => $products,
            'sort' => $sort,
        ]);
    }
}
