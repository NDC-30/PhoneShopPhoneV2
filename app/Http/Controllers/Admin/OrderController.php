<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Variant;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // 1. Danh sách đơn hàng
    public function index()
    {
        $orders = Order::with(['details.variant.product'])->orderBy('order_id', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    // 2. Giao diện Tạo đơn hàng mới (Bán tại quầy)
    public function create()
    {
        $variants = Variant::with('product')->where('status', 1)->where('stock', '>', 0)->get();
        return view('admin.orders.create', compact('variants'));
    }

    // 3. Xử lý lưu đơn hàng mới vào DB
    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required',
            'receiver_phone' => 'required',
            'variant_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = Variant::findOrFail($request->variant_id);
        $totalPrice = $variant->price * $request->quantity;

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(6)),
            'total_amount' => $totalPrice,
            'grand_total' => $totalPrice,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'province' => $request->province ?? 'N/A',
            'district' => $request->district ?? 'N/A',
            'ward' => $request->ward ?? 'N/A',
            'shipping_address' => $request->shipping_address ?? 'Mua tại quầy',
            'payment_method' => $request->payment_method ?? 'CASH',
            'status' => 'completed', 
        ]);

        OrderDetail::create([
            'order_id' => $order->order_id,
            'variant_id' => $variant->variant_id,
            'quantity' => $request->quantity,
            'unit_price' => $variant->price,
            'subtotal' => $totalPrice
        ]);

        $variant->decrement('stock', $request->quantity);

        return redirect()->route('admin.orders.index')->with('success', 'Đã tạo đơn hàng mới thành công!');
    }

    // 4. XEM CHI TIẾT ĐƠN HÀNG & THÔNG TIN SHIPPER
    public function show($id)
    {
        $order = Order::with(['details.variant.product', 'shipping'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // 5. CẬP NHẬT TRẠNG THÁI & SHIPPER & HOÀN KHO KHI HỦY
    public function update(Request $request, string $id)
    {
        if (in_array($request->status, ['shipping', 'completed'])) {
            $request->validate([
                'carrier' => 'required',
                'tracking_number' => 'required'
            ], [
                'carrier.required' => 'Phải chọn Đơn vị vận chuyển trước khi giao hàng chứ!',
                'tracking_number.required' => 'Mã Vận Đơn không được để trống khi đang giao hàng!'
            ]);
        }

        $order = Order::with('details')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Logic Hoàn Kho khi Hủy Đơn
        if ($newStatus == 'cancelled' && $oldStatus != 'cancelled') {
            foreach ($order->details as $detail) {
                $variant = Variant::find($detail->variant_id);
                if ($variant) {
                    $variant->increment('stock', $detail->quantity); 
                }
            }
        }

        // Logic Trừ Kho khi Khôi phục đơn từ Hủy
        if ($oldStatus == 'cancelled' && $newStatus != 'cancelled') {
            foreach ($order->details as $detail) {
                $variant = Variant::find($detail->variant_id);
                if ($variant && $variant->stock >= $detail->quantity) {
                    $variant->decrement('stock', $detail->quantity);
                } else {
                    return redirect()->back()->with('error', 'Không đủ hàng trong kho để khôi phục đơn này!');
                }
            }
        }
        
        $order->update(['status' => $newStatus]);

        // Lưu bảng Shipping
        if ($request->has('carrier') || $request->has('tracking_number')) {
            Shipping::updateOrCreate(
                ['order_id' => $order->order_id], 
                [
                    'carrier' => $request->carrier,
                    'tracking_number' => $request->tracking_number,
                    'shipping_fee' => $request->shipping_fee ?? 0,
                    'status' => $newStatus
                ]
            );
        }

        return redirect()->back()->with('success', 'Đã cập nhật Trạng thái & Vận chuyển!');
    }

    // 6. XÓA VĨNH VIỄN ĐƠN HÀNG
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        OrderDetail::where('order_id', $order->order_id)->delete();
        Shipping::where('order_id', $order->order_id)->delete();
        
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Đã xóa vĩnh viễn đơn hàng và các dữ liệu liên quan!');
    }
}