<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index() {
        $vouchers = Voucher::orderBy('voucher_id', 'desc')->get();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function store(Request $request) {
        $request->validate([
            'code' => 'required|unique:vouchers,code',
            'discount_value' => 'required|numeric',
            'discount_type' => 'required|in:fixed,percent'
        ]);
        
        Voucher::create([
            'code' => strtoupper($request->code),
            'name' => $request->name ?? $request->code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value ?? 0,
            'max_discount' => $request->max_discount ?? null,
            'usage_limit' => $request->usage_limit ?? 0,
            'status' => 1
        ]);
        
        return redirect()->back()->with('success', 'Tạo voucher thành công!');
    }

    public function destroy($id) {
        $voucher = Voucher::where('voucher_id', $id)->first();
        if ($voucher) {
            $voucher->delete();
        }
        return redirect()->back()->with('success', 'Đã xóa voucher!');
    }
}