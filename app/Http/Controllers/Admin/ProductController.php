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
        $request->validate([
            'name' => 'required|unique:products,name',
            'brand_id' => 'required',
            'category_id' => 'required',
        ], [
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
        ]);

        Product::create([
            'name' => trim($request->name),
            'slug' => Str::slug($request->name),
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'description' => $request->description,

            // luôn ở trạng thái chưa bán
            'status' => 0
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
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
        $request->validate([
            'name' => 'required|unique:products,name,' . $id . ',product_id',
        ], [
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'name' => trim($request->name),
            'slug' => Str::slug($request->name),
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // 5. XÓA SẢN PHẨM (MỚI THÊM)
    public function destroy($id)
{
    $product = Product::with('variants')->findOrFail($id);

    // Kiểm tra sản phẩm đã có trong đơn hàng chưa
    $hasOrders = \App\Models\OrderDetail::whereIn(
        'variant_id',
        $product->variants->pluck('variant_id')
    )->exists();

    if ($hasOrders) {
        return back()->with(
            'error',
            'Sản phẩm đã phát sinh đơn hàng nên không thể xóa!'
        );
    }

    // Xóa các phiên bản trước
    Variant::where(
        'product_id',
        $product->product_id
    )->delete();

    // Xóa sản phẩm
    $product->delete();

    return back()->with(
        'success',
        'Xóa sản phẩm thành công!'
    );
}
    public function publish($id)
    {
        $product = Product::findOrFail($id);

        if ($product->variants()->count() < 1) {
            return back()->with(
                'error',
                'Sản phẩm phải có ít nhất 1 phiên bản trước khi đăng bán!'
            );
        }

        $product->update([
            'status' => 1
        ]);

        return back()->with(
            'success',
            'Đăng bán sản phẩm thành công!'
        );
    }

    public function unpublish($id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'status' => 0
        ]);

        return back()->with(
            'success',
            'Đã dừng bán sản phẩm!'
        );
    }
}
