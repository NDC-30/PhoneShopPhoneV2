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
    // 1. HIỂN THỊ DANH SÁCH
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

    // 2. LƯU SẢN PHẨM MỚI (Khung code cũ của sếp - Xịn giữ nguyên)
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

    // 3. HIỂN THỊ FORM CẬP NHẬT (MỚI THÊM)
    public function edit($id)
    {
        // Phải load kèm biến thể để mang ra view hiển thị
        $product = Product::with('variants')->findOrFail($id); 
        $categories = Category::all();
        $brands = Brand::all();
        
        // Trả về file giao diện edit.blade.php
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    // 4. XỬ LÝ LƯU DỮ LIỆU CẬP NHẬT (MỚI THÊM)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'status' => $request->status ?? 1
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm thành công!');
    }

    // 5. XÓA SẢN PHẨM (MỚI THÊM)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Xóa sản phẩm cha (tùy thuộc vào CSDL mà sếp có cần xóa thủ công biến thể không)
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm thành công!');
    }
}