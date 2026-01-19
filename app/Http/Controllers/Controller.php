<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
//    public function totalCategoryProductCount(){
//        $categories = Category::all();
//        foreach ($categories as $category) {
//            $totalProducts = Product::where('category', $category->id)->count();
//            $category->total_products = $totalProducts;
//            $category->save();
//        }
//        return Category::sum('total_products');
//    }
}
