<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VnpayController extends Controller
{
    /** Tạo URL thanh toán và chuyển hướng khách sang cổng VNPay */
    public function create(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order->order_id);
        }

        $vnp_TmnCode    = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url        = config('vnpay.url');
        $vnp_ReturnUrl  = route('vnpay.return');

        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => (int) $order->grand_total * 100,   // VNPay nhân 100
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => $now->format('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $request->ip(),
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => 'Thanh toan don hang ' . $order->order_number,
            "vnp_OrderType"  => "other",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $order->order_id,                  // mã tham chiếu = id đơn
            "vnp_ExpireDate" => $now->copy()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($inputData);
        $hashdata = '';
        $query    = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnp_Url .= '?' . $query;
        if ($vnp_HashSecret) {
            $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $secureHash;
        }

        return redirect()->away($vnp_Url);
    }

    /** VNPay chuyển khách về đây sau khi thanh toán (hiển thị kết quả) */
    public function return(Request $request)
    {
        [$valid, $inputData] = $this->verify($request);

        $order = Order::find($request->query('vnp_TxnRef'));
        if (!$order) {
            return redirect()->route('account.orders')->with('error', 'Không tìm thấy đơn hàng.');
        }
        if (!$valid) {
            return redirect()->route('account.order.show', $order->order_id)
                ->with('error', 'Sai chữ ký dữ liệu thanh toán.');
        }

        $ok = $request->query('vnp_ResponseCode') === '00'
           && $request->query('vnp_TransactionStatus') === '00';

        if ($ok) {
            $this->markPaid($order, $request->query('vnp_TransactionNo'));
            app(CartService::class)->clear();   // thanh toán xong mới dọn giỏ
            return redirect()->route('checkout.success', $order->order_id)
                ->with('success', 'Thanh toán VNPay thành công!');
        }

        $order->update(['payment_status' => 'failed']);
        return redirect()->route('account.order.show', $order->order_id)
            ->with('error', 'Thanh toán VNPay không thành công (mã ' . $request->query('vnp_ResponseCode') . ').');
    }

    /** IPN: VNPay gọi server-to-server (chỉ chạy khi web có domain public, vd dùng ngrok) */
    public function ipn(Request $request)
    {
        [$valid] = $this->verify($request);
        if (!$valid) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Checksum']);
        }

        $order = Order::find($request->query('vnp_TxnRef'));
        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }
        if ((int) $request->query('vnp_Amount') !== (int) $order->grand_total * 100) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }
        if ($order->payment_status === 'paid') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($request->query('vnp_ResponseCode') === '00') {
            $this->markPaid($order, $request->query('vnp_TransactionNo'));
        } else {
            $order->update(['payment_status' => 'failed']);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    /** Kiểm tra chữ ký HMAC-SHA512 của dữ liệu VNPay trả về */
    private function verify(Request $request): array
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = [];
        foreach ($request->query() as $key => $value) {
            if (substr($key, 0, 4) === 'vnp_') $inputData[$key] = $value;
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        return [hash_equals($secureHash, $vnp_SecureHash), $inputData];
    }

    private function markPaid(Order $order, ?string $transactionNo): void
    {
        if ($order->payment_status === 'paid') return;

        $order->update(['payment_status' => 'paid']);
        if ($order->payment) {
            $order->payment->update([
                'status'           => 'paid',
                'transaction_code' => $transactionNo,
                'payment_date'     => now(),
            ]);
        }
    }
}
