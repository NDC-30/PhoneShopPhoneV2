<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 

class CategoryController extends Controller
{
    // 1. HIỂN THỊ DANH SÁCH
    public function index()
    {
        // Lấy dữ liệu, sắp xếp thằng mới thêm lên đầu
        $categories = Category::orderBy('category_id', 'desc')->get(); 
        return view('admin.categories.index', compact('categories'));
    }

    // 2. XỬ LÝ THÊM MỚI TỪ MODAL
    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Bắt buộc phải nhập tên danh mục!',
        ]);

        // Lưu vào MySQL
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name), 
            'status' => $request->status ?? 1,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    // 3. HIỂN THỊ TRANG CHỈNH SỬA
    public function edit($id) 
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    // 4. LƯU DỮ LIỆU ĐÃ CHỈNH SỬA
    public function update(Request $request, $id) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Bắt buộc phải nhập tên danh mục!',
        ]);

        $category = Category::findOrFail($id);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? 1,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    // 5. XỬ LÝ XÓA DỮ LIỆU
    public function destroy($id) 
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'LỖI: Không thể xóa! Đang có sản phẩm nằm trong danh mục này.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục an toàn!');
    }
}