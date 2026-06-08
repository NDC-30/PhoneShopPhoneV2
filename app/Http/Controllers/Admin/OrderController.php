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
    // Nhãn tiếng Việt cho trạng thái
    private array $statusLabel = [
        'pending'    => 'Chờ xác nhận',
        'processing' => 'Đã xác nhận',
        'shipping'   => 'Đang giao hàng',
        'completed'  => 'Hoàn thành',
        'returned'   => 'Hoàn trả',
        'cancelled'  => 'Đã hủy',
    ];

    // Quy trình chuyển trạng thái hợp lệ (completed/cancelled/returned = trạng thái cuối, khóa)
    private array $flow = [
        'pending'    => ['processing', 'cancelled'],
        'processing' => ['shipping', 'cancelled'],
        'shipping'   => ['completed', 'returned'],
        'completed'  => [],
        'cancelled'  => [],
        'returned'   => [],
    ];

    public function index()
    {
        $orders = Order::with(['details.variant.product'])->orderBy('order_id', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $variants = Variant::with('product')->where('status', 1)->where('stock', '>', 0)->get();
        return view('admin.orders.create', compact('variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_name'  => 'required',
            'receiver_phone' => 'required',
            'variant_id'     => 'required',
            'quantity'       => 'required|integer|min:1',
        ]);

        $variant = Variant::findOrFail($request->variant_id);

        if ($variant->stock < $request->quantity) {
            return back()->with('error', 'Không đủ hàng trong kho (chỉ còn ' . $variant->stock . ' máy).')->withInput();
        }

        $totalPrice = $variant->price * $request->quantity;

        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(Str::random(6)),
            'total_amount'     => $totalPrice,
            'grand_total'      => $totalPrice,
            'receiver_name'    => $request->receiver_name,
            'receiver_phone'   => $request->receiver_phone,
            'province'         => $request->province ?? 'N/A',
            'district'         => $request->district ?? 'N/A',
            'ward'             => $request->ward ?? 'N/A',
            'shipping_address' => $request->shipping_address ?? 'Mua tại quầy',
            'payment_method'   => $request->payment_method ?? 'cash',
            'status'           => 'completed',
        ]);

        OrderDetail::create([
            'order_id'   => $order->order_id,
            'variant_id' => $variant->variant_id,
            'quantity'   => $request->quantity,
            'unit_price' => $variant->price,
            'subtotal'   => $totalPrice,
        ]);

        $variant->decrement('stock', $request->quantity);
        $variant->increment('sold', $request->quantity);

        return redirect()->route('admin.orders.index')->with('success', 'Đã tạo đơn hàng mới thành công!');
    }

    public function show($id)
    {
        $order = Order::with(['details.variant.product', 'shipping'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // API: tự sinh mã vận đơn theo ĐVVC (có tiền tố để dễ nhận diện)
    public function generateTracking(Request $request)
    {
        $prefixMap = [
            'Giao Hàng Nhanh' => 'GHN',
            'GHTK'            => 'GHTK',
            'Viettel Post'    => 'VTP',
        ];
        $carrier = $request->input('carrier');
        if (!$carrier) {
            return response()->json(['ok' => false, 'message' => 'Vui lòng chọn đơn vị vận chuyển trước.'], 422);
        }
        $prefix = $prefixMap[$carrier] ?? 'PS';
        // Định dạng: PREFIX-yymmdd-RANDOM6  (vd: GHN-260607-A8K2QX)
        $code = $prefix . '-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));

        return response()->json(['ok' => true, 'tracking_number' => $code]);
    }

    public function update(Request $request, string $id)
    {
        $order     = Order::with('details')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->input('status', $oldStatus);

        $statusChanging = $newStatus !== $oldStatus;

        if ($statusChanging) {
            // 1) Đơn đã chốt (hoàn thành/hủy/hoàn trả) -> KHÓA
            if (in_array($oldStatus, ['completed', 'cancelled', 'returned'])) {
                return back()->with('error',
                    'Đơn đang ở trạng thái "' . ($this->statusLabel[$oldStatus] ?? $oldStatus) . '" — đây là trạng thái cuối, không thể chuyển sang trạng thái khác.');
            }

            // 2) Chuyển sai quy trình -> báo lý do
            if (!in_array($newStatus, $this->flow[$oldStatus] ?? [])) {
                $allowVi = array_map(fn ($s) => $this->statusLabel[$s] ?? $s, $this->flow[$oldStatus] ?? []);
                return back()->with('error',
                    'Không thể chuyển từ "' . ($this->statusLabel[$oldStatus] ?? $oldStatus) . '" sang "' . ($this->statusLabel[$newStatus] ?? $newStatus) . '". '
                    . (count($allowVi) ? 'Từ trạng thái này chỉ có thể chuyển sang: ' . implode(' hoặc ', $allowVi) . '.' : ''));
            }

            // 3) Bắt đầu giao hàng -> bắt buộc có ĐVVC + mã vận đơn
            if ($newStatus === 'shipping') {
                $request->validate(
                    ['carrier' => 'required', 'tracking_number' => 'required'],
                    [
                        'carrier.required'         => 'Phải chọn Đơn vị vận chuyển trước khi giao hàng!',
                        'tracking_number.required' => 'Cần có Mã vận đơn (bấm "Tạo mã tự động") trước khi giao hàng!',
                    ]
                );
            }

            // 4) Hoàn kho khi chuyển sang hủy / hoàn trả
            if (in_array($newStatus, ['cancelled', 'returned'])) {
                foreach ($order->details as $detail) {
                    if ($variant = Variant::find($detail->variant_id)) {
                        $variant->increment('stock', $detail->quantity);
                        if ($variant->sold >= $detail->quantity) {
                            $variant->decrement('sold', $detail->quantity);
                        }
                    }
                }
            }

            $order->update(['status' => $newStatus]);
        }

        // Lưu thông tin vận chuyển (kể cả khi không đổi trạng thái)
        if ($request->filled('carrier') || $request->filled('tracking_number')) {
            Shipping::updateOrCreate(
                ['order_id' => $order->order_id],
                [
                    'carrier'         => $request->carrier,
                    'tracking_number' => $request->tracking_number,
                    'shipping_fee'    => $request->shipping_fee ?? 0,
                    'status'          => $order->status,
                ]
            );
        }

        return back()->with('success',
            $statusChanging
                ? 'Đã chuyển trạng thái sang "' . ($this->statusLabel[$newStatus] ?? $newStatus) . '".'
                : 'Đã lưu thông tin vận chuyển.');
    }

    public function destroy($id)
    {
        // Không cho xóa đơn hàng — đơn là dữ liệu lịch sử, chỉ có thể Hủy / Hoàn trả.
        return redirect()->route('admin.orders.index')
            ->with('error', 'Không thể xóa đơn hàng. Đơn hàng là dữ liệu lịch sử kinh doanh — chỉ có thể chuyển sang "Đã hủy" hoặc "Hoàn trả".');
    }
}
