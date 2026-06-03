<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Variant;
use App\Models\Voucher;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected CartService $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $subtotal    = $items->sum('line_total');
        $voucher     = session('applied_voucher');
        $discount    = $voucher ? ($voucher['discount'] ?? 0) : 0;
        $shippingFee = $subtotal >= 5000000 ? 0 : 30000;   // freeship đơn từ 5tr
        $grandTotal  = max(0, $subtotal - $discount) + $shippingFee;

        $user = auth()->user();

        return view('customer.checkout.index', compact(
            'items', 'subtotal', 'voucher', 'discount', 'shippingFee', 'grandTotal', 'user'
        ));
    }

    /** AJAX: kiểm tra & áp mã voucher cho tổng tiền hiện tại */
    public function applyVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $code = strtoupper(trim($request->code));

        $voucher = Voucher::where('code', $code)->first();
        if (!$voucher || !$voucher->isUsable()) {
            session()->forget('applied_voucher');
            return response()->json(['ok' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn.'], 422);
        }

        $subtotal = $this->cart->subtotal();
        if ($subtotal < $voucher->min_order_value) {
            return response()->json([
                'ok'      => false,
                'message' => 'Đơn tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . '₫ để dùng mã này.',
            ], 422);
        }

        $discount = $voucher->calcDiscount($subtotal);

        session(['applied_voucher' => [
            'voucher_id' => $voucher->voucher_id,
            'code'       => $voucher->code,
            'name'       => $voucher->name,
            'discount'   => $discount,
        ]]);

        return response()->json([
            'ok'             => true,
            'message'        => 'Áp mã thành công! Giảm ' . number_format($discount, 0, ',', '.') . '₫',
            'discount'       => $discount,
            'discount_label' => number_format($discount, 0, ',', '.') . '₫',
        ]);
    }

    public function removeVoucher()
    {
        session()->forget('applied_voucher');
        return back();
    }

    /** Công khai: xem trước số tiền giảm của 1 mã cho 1 mức giá (dùng ở trang sản phẩm) */
    public function preview(Request $request)
    {
        $request->validate([
            'code'   => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', strtoupper(trim($request->code)))->first();
        if (!$voucher || !$voucher->isUsable()) {
            return response()->json(['ok' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn.'], 422);
        }
        if ($request->amount < $voucher->min_order_value) {
            return response()->json([
                'ok'      => false,
                'message' => 'Cần đơn tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . '₫.',
            ], 422);
        }

        $discount = $voucher->calcDiscount((float) $request->amount);
        return response()->json([
            'ok'       => true,
            'message'  => 'Mã hợp lệ — giảm ' . number_format($discount, 0, ',', '.') . '₫. Áp dụng khi thanh toán.',
            'discount' => $discount,
        ]);
    }

    /** Đặt hàng */
    public function place(Request $request)
    {
        $data = $request->validate([
            'receiver_name'    => 'required|string|max:100',
            'receiver_phone'   => 'required|string|max:20',
            'province'         => 'required|string|max:50',
            'district'         => 'required|string|max:50',
            'ward'             => 'required|string|max:50',
            'shipping_address' => 'required|string|max:255',
            'customer_note'    => 'nullable|string',
            'payment_method'   => 'required|in:cod,bank',
        ]);

        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        $subtotal    = $items->sum('line_total');
        $voucherData = session('applied_voucher');
        $voucher     = null;
        $discount    = 0;

        // Xác thực lại voucher tại thời điểm đặt (tránh gian lận)
        if ($voucherData) {
            $voucher = Voucher::find($voucherData['voucher_id']);
            if ($voucher && $voucher->isUsable() && $subtotal >= $voucher->min_order_value) {
                $discount = $voucher->calcDiscount($subtotal);
            } else {
                $voucher = null;
            }
        }

        $shippingFee = $subtotal >= 5000000 ? 0 : 30000;
        $grandTotal  = max(0, $subtotal - $discount) + $shippingFee;

        try {
            $order = DB::transaction(function () use ($data, $items, $subtotal, $discount, $shippingFee, $grandTotal, $voucher) {

                $order = Order::create([
                    'user_id'         => auth()->id(),
                    'voucher_id'      => $voucher?->voucher_id,
                    'order_number'    => 'PS' . now()->format('ymd') . strtoupper(Str::random(5)),
                    'total_amount'    => $subtotal,
                    'discount_amount' => $discount,
                    'shipping_fee'    => $shippingFee,
                    'tax'             => 0,
                    'grand_total'     => $grandTotal,
                    'status'          => 'pending',
                    'payment_method'  => $data['payment_method'],
                    'payment_status'  => 'pending',
                    'receiver_name'   => $data['receiver_name'],
                    'receiver_phone'  => $data['receiver_phone'],
                    'province'        => $data['province'],
                    'district'        => $data['district'],
                    'ward'            => $data['ward'],
                    'shipping_address' => $data['shipping_address'],
                    'customer_note'   => $data['customer_note'] ?? null,
                ]);

                foreach ($items as $item) {
                    $variant = Variant::lockForUpdate()->find($item->variant->variant_id);
                    if (!$variant || $variant->stock < $item->quantity) {
                        throw new \RuntimeException('Sản phẩm "' . $variant?->label . '" không đủ hàng.');
                    }

                    OrderDetail::create([
                        'order_id'    => $order->order_id,
                        'variant_id'  => $variant->variant_id,
                        'quantity'    => $item->quantity,
                        'unit_price'  => $variant->price,
                        'subtotal'    => $variant->price * $item->quantity,
                    ]);

                    // Trừ kho, cộng đã bán
                    $variant->decrement('stock', $item->quantity);
                    $variant->increment('sold', $item->quantity);
                }

                // Bản ghi thanh toán
                Payment::create([
                    'order_id'       => $order->order_id,
                    'amount'         => $grandTotal,
                    'payment_method' => $data['payment_method'],
                    'status'         => $data['payment_method'] === 'cod' ? 'pending' : 'pending',
                ]);

                // Bản ghi vận chuyển khởi tạo
                Shipping::create([
                    'order_id'     => $order->order_id,
                    'shipping_fee' => $shippingFee,
                    'status'       => 'preparing',
                ]);

                // Tăng lượt dùng voucher
                if ($voucher) {
                    $voucher->increment('used_count');
                }

                return $order;
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Đặt hàng thất bại, vui lòng thử lại.');
        }

        // Dọn giỏ + voucher
        $this->cart->clear();

        return redirect()->route('checkout.success', $order->order_id);
    }

    public function success(Order $order)
    {
        // Chỉ chủ đơn mới xem được
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load('details.variant.product', 'shipping', 'payment');

        return view('customer.checkout.success', compact('order'));
    }
}
