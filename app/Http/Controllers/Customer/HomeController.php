<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with([
            'brand',
            'variants'
        ])
        ->where('status', 1)
        ->latest()
        ->take(12)
        ->get();

        $brands = Brand::where('status', 1)->get();

        $categories = Category::where('status', 1)->get();

        return view(
            'customer.home.index',
            compact(
                'featuredProducts',
                'brands',
                'categories'
            )
        );
    }
}