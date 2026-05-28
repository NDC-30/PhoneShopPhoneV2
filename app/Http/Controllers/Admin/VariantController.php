<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariantController extends Controller
{
    public function index($product_id)
    {
        $product = Product::findOrFail($product_id);
        
        // Lấy biến thể và load sẵn các thuộc tính EAV đã lưu
        $variants = Variant::with('attributeValues.attribute')
                           ->where('product_id', $product_id)
                           ->orderBy('variant_id', 'desc')
                           ->get();
                           
        return view('admin.variants.index', compact('product', 'variants'));
    }

    public function store(Request $request, $product_id)
    {
        // 1. Lưu Ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url; 
        }

        // 2. Lưu Biến thể
        $variant = Variant::create([
            'product_id' => $product_id,
            'sku' => $request->sku ?? strtoupper(Str::random(8)),
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'image' => $imagePath,
            'status' => 1
        ]);

        // 3. LƯU EAV (Đã sửa từ 'attributes' thành 'specs' để tránh lỗi Laravel)
        if ($request->has('specs')) {
            $specs = $request->input('specs'); // Lấy mảng dữ liệu từ form
            
            foreach ($specs as $attrName => $attrValue) {
                if (!empty($attrValue)) {
                    // Tạo Tên (RAM, ROM...)
                    $attribute = Attribute::firstOrCreate(
                        ['name' => $attrName],
                        ['display_name' => $attrName]
                    );

                    // Tạo Giá trị (8GB, 256GB...)
                    $val = AttributeValue::firstOrCreate(
                        ['attribute_id' => $attribute->attribute_id, 'value' => $attrValue]
                    );

                    // Gắn vào biến thể
                    $variant->attributeValues()->attach($val->value_id);
                }
            }
        }

        return redirect()->route('admin.products.variants.index', $product_id)->with('success', 'Đã lưu thông số kỹ thuật thành công!');
    }
    // 1. GỌI FORM SỬA: Đổ dữ liệu cũ ra Form
    public function edit($product_id, $variant_id)
    {
        $product = Product::findOrFail($product_id);
        $variant = Variant::with('attributeValues.attribute')->findOrFail($variant_id);

        // Tuyệt chiêu: Ép mảng EAV phức tạp thành dạng Key => Value (VD: 'RAM' => '8GB') để dễ in ra Form
        $currentSpecs = [];
        foreach ($variant->attributeValues as $attrVal) {
            $currentSpecs[$attrVal->attribute->name] = $attrVal->value;
        }

        return view('admin.variants.edit', compact('product', 'variant', 'currentSpecs'));
    }

    // 2. LƯU DỮ LIỆU SỬA: Xóa thông số cũ, cập nhật thông số mới
    public function update(Request $request, $product_id, $variant_id)
    {
        $variant = Variant::findOrFail($variant_id);

        $imagePath = $variant->image; 
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $variant->update([
            'sku' => $request->sku,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'image' => $imagePath
        ]);

        if ($request->has('specs')) {
            $variant->attributeValues()->detach(); 

            foreach ($request->input('specs') as $attrName => $attrValue) {
                if (!empty($attrValue)) {
                    $attribute = Attribute::firstOrCreate(['name' => $attrName], ['display_name' => $attrName]);
                    $val = AttributeValue::firstOrCreate(['attribute_id' => $attribute->attribute_id, 'value' => $attrValue]);
                    $variant->attributeValues()->attach($val->value_id);
                }
            }
        }

        return redirect()->route('admin.products.variants.index', $product_id)->with('success', 'Đã cập nhật phiên bản thành công!');
    }

    // 3. XÓA BIẾN THỂ
    public function destroy($product_id, $variant_id)
    {
        $variant = Variant::findOrFail($variant_id);
        
        $variant->attributeValues()->detach(); 
        
        $variant->delete();

        return redirect()->route('admin.products.variants.index', $product_id)->with('success', 'Đã xóa phiên bản vĩnh viễn!');
    }
}