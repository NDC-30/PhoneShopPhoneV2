<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        $items    = $this->cart->items();
        $subtotal = $items->sum('line_total');
        $voucher  = session('applied_voucher');

        return view('customer.cart.index', compact('items', 'subtotal', 'voucher'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|integer|exists:variants,variant_id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $variant = Variant::findOrFail($data['variant_id']);
        $qty     = $data['quantity'] ?? 1;

        if ($variant->stock <= 0) {
            return $this->respond($request, false, 'Sản phẩm đã hết hàng.');
        }

        $this->cart->add($variant->variant_id, $qty);

        return $this->respond($request, true, 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|integer',
            'quantity'   => 'required|integer|min:0',
        ]);

        $this->cart->update($data['variant_id'], $data['quantity']);

        if ($request->ajax()) {
            $items = $this->cart->items();
            return response()->json([
                'ok'       => true,
                'count'    => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
            ]);
        }
        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function remove(Request $request)
    {
        $request->validate(['variant_id' => 'required|integer']);
        $this->cart->remove($request->variant_id);
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ.');
    }

    public function clear()
    {
        $this->cart->clear();
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }

    private function respond(Request $request, bool $ok, string $msg)
    {
        if ($request->ajax()) {
            return response()->json([
                'ok'      => $ok,
                'message' => $msg,
                'count'   => $this->cart->count(),
            ], $ok ? 200 : 422);
        }
        return back()->with($ok ? 'success' : 'error', $msg);
    }
}
