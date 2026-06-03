<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['brand', 'images', 'variants']);

        // Tìm kiếm theo tên
        if ($q = trim($request->get('q', ''))) {
            $query->where('name', 'like', "%{$q}%");
        }

        // Lọc theo thương hiệu (slug hoặc id, cho phép chọn nhiều)
        if ($brand = $request->get('brand')) {
            $brands = is_array($brand) ? $brand : [$brand];
            $query->whereHas('brand', function ($b) use ($brands) {
                $b->whereIn('slug', $brands)->orWhereIn('brand_id', $brands);
            });
        }

        // Lọc theo danh mục
        if ($cat = $request->get('category')) {
            $query->where('category_id', $cat);
        }

        // Giá nhỏ nhất của biến thể để sắp xếp
        $query->withMin(['variants as min_price' => fn ($v) => $v->where('status', 1)], 'price');

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc'  => $query->orderBy('min_price', 'asc'),
            'price_desc' => $query->orderBy('min_price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->latest('product_id'),
        };

        $products   = $query->paginate(12)->withQueryString();
        $brands     = Brand::where('status', 1)->withCount('products')->get();
        $categories = Category::where('status', 1)->whereNull('parent_id')->get();

        return view('customer.shop.index', compact('products', 'brands', 'categories', 'sort'));
    }
}
