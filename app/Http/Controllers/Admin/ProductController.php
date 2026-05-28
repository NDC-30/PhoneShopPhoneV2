<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])
            ->withCount('variants')
            ->orderBy('product_id', 'desc')
            ->get();
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        // 1. Lưu sản phẩm cha
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'status' => 1
        ]);

        // 2. Nếu có biến thể thì lưu luôn (EAV)
        if ($request->has('variants')) {
            foreach ($request->variants as $varData) {
                $imagePath = null;
                if (isset($varData['image']) && $varData['image']->isValid()) {
                    $imagePath = $varData['image']->store('variants', 'public');
                }

                $variant = Variant::create([
                    'product_id' => $product->product_id,
                    'sku' => $varData['sku'] ?? strtoupper(Str::random(8)),
                    'price' => $varData['price'],
                    'stock' => $varData['stock'] ?? 0,
                    'image' => $imagePath,
                    'status' => 1
                ]);

                if (isset($varData['attributes'])) {
                    foreach ($varData['attributes'] as $attrName => $attrValue) {
                        if (!empty($attrValue)) {
                            $attribute = Attribute::firstOrCreate(['name' => $attrName]);
                            $val = AttributeValue::firstOrCreate([
                                'attribute_id' => $attribute->attribute_id, 
                                'value' => $attrValue
                            ]);
                            $variant->attributeValues()->attach($val->value_id);
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }
}