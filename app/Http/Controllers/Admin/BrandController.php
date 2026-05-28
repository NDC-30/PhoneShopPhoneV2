<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    // 1. Lên danh sách Thương hiệu
    public function index()
    {
        $brands = Brand::orderBy('brand_id', 'desc')->get(); 
        return view('admin.brands.index', compact('brands'));
    }

    // 2. Thêm mới
    public function store(Request $request) 
    {
        $request->validate(['name' => 'required|string|max:255'], ['name.required' => 'Chưa nhập tên thương hiệu!']);

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    // 3. Gọi form sửa
    public function edit($id){
    $brand = Brand::findOrFail($id);
    
    return view('admin.brands.edit', compact('brand'));
    }

    // 4. Lưu cập nhật
    public function update(Request $request, $id) 
    {
        $request->validate(['name' => 'required|string|max:255'], ['name.required' => 'Chưa nhập tên thương hiệu!']);
        $brand = Brand::findOrFail($id);
        
        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thành công!');
    }

    // 5. Xóa an toàn
    public function destroy($id) 
    {
        $brand = Brand::findOrFail($id);

        if ($brand->products()->count() > 0) {
            return redirect()->route('admin.brands.index')->with('error', 'LỖI: Đang có sản phẩm thuộc hãng này, không thể xóa!');
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Đã xóa thương hiệu!');
    }
}