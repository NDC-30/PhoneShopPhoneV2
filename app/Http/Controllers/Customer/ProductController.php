<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with([
            'brand',
            'variants'
        ])
        ->where('status', 1)
        ->paginate(12);

        return view(
            'customer.products.index',
            compact('products')
        );
    }

    public function show($slug)
    {
        $product = Product::with([
            'brand',
            'variants.attributeValues.attribute'
        ])
        ->where('slug', $slug)
        ->firstOrFail();

        $variant = $product->variants->first();

        return view(
            'customer.products.show',
            compact(
                'product',
                'variant'
            )
        );
    }
}
