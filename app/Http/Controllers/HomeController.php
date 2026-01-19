<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * HomeController - Menangani halaman publik
 */
class HomeController extends Controller
{
    /**
     * Homepage
     */
    public function index()
    {
        $banners = Banner::active()->ordered()->take(5)->get();
        
        $featuredProducts = Product::with(['category', 'admin'])
            ->active()
            ->inStock()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::with(['category', 'admin'])
            ->active()
            ->inStock()
            ->latest()
            ->take(12)
            ->get();

        $categories = Category::active()
            ->ordered()
            ->withCount(['products' => function ($query) {
                $query->active()->inStock();
            }])
            ->get();

        $announcements = Announcement::active()
            ->ordered()
            ->take(3)
            ->get();

        return Inertia::render('Home', [
            'banners' => $banners,
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'categories' => $categories,
            'announcements' => $announcements,
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categorySlug = $request->get('category');
        $sort = $request->get('sort', 'latest');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $products = Product::with(['category', 'admin'])
            ->active()
            ->inStock();

        // Search by keyword
        if ($query) {
            $products->search($query);
        }

        // Filter by category
        if ($categorySlug) {
            $products->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by price range
        if ($minPrice) {
            $products->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $products->where('price', '<=', $maxPrice);
        }

        // Sort
        switch ($sort) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'popular':
                $products->orderBy('sold_count', 'desc');
                break;
            case 'latest':
            default:
                $products->latest();
                break;
        }

        $products = $products->paginate(12)->withQueryString();

        $categories = Category::active()->ordered()->get();

        return Inertia::render('Search', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'q' => $query,
                'category' => $categorySlug,
                'sort' => $sort,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ],
        ]);
    }

    /**
     * Category page
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::with(['category', 'admin'])
            ->where('category_id', $category->id)
            ->active()
            ->inStock()
            ->latest()
            ->paginate(12);

        $categories = Category::active()->ordered()->get();

        return Inertia::render('Category', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
