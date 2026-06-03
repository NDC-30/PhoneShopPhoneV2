<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $with = ['brand', 'images', 'variants'];

        $featured = Product::active()->with($with)
            ->where('is_featured', 1)
            ->latest('product_id')->take(8)->get();

        $newest = Product::active()->with($with)
            ->latest('product_id')->take(8)->get();

        if ($featured->isEmpty()) $featured = $newest;

        $brands = Brand::where('status', 1)->get();

        return view('customer.home.index', compact('featured', 'newest', 'brands'));
    }

    public function contact()
    {
        return view('customer.home.contact');
    }
}
