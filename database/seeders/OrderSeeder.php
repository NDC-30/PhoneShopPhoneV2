<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Variant;
use App\Models\Product;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // 1. Kiểm tra xem kho có máy nào chưa. Nếu chưa có thì tự tạo 1 con máy ảo để có hàng mà mua
        $variant = Variant::first();
        if (!$variant) {
            $product = Product::create([
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'status' => 1
            ]);
            $variant = Variant::create([
                'product_id' => $product->product_id,
                'sku' => 'IP15PM-256GB',
                'price' => 29000000,
                'stock' => 100,
                'status' => 1
            ]);
        }

        // 2. TẠO ĐƠN SỐ 1: Trạng thái Chờ duyệt (COD)
        $order1 = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(6)),
            'total_amount' => $variant->price * 2,
            'grand_total' => $variant->price * 2,
            'receiver_name' => 'Nguyễn Đăng Chương',
            'receiver_phone' => '0987654321',
            'province' => 'Hà Nội',
            'district' => 'Hai Bà Trưng',
            'ward' => 'Bách Khoa',
            'shipping_address' => 'Số 17 Tạ Quang Bửu',
            'payment_method' => 'COD',
            'status' => 'pending',
        ]);

        OrderDetail::create([
            'order_id' => $order1->order_id,
            'variant_id' => $variant->variant_id,
            'quantity' => 2,
            'unit_price' => $variant->price,
            'subtotal' => $variant->price * 2
        ]);

        // 3. TẠO ĐƠN SỐ 2: Trạng thái Đang giao (Đã thanh toán Bank)
        $order2 = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(6)),
            'total_amount' => $variant->price,
            'grand_total' => $variant->price,
            'receiver_name' => 'Nguyen Văn B',
            'receiver_phone' => '0123456789',
            'province' => 'Hà Nội',
            'district' => 'Hoàng Mai',
            'ward' => 'Đại Kim',
            'shipping_address' => '123 Đường Giải Phóng',
            'payment_method' => 'BANK_TRANSFER',
            'status' => 'pending',
        ]);

        OrderDetail::create([
            'order_id' => $order2->order_id,
            'variant_id' => $variant->variant_id,
            'quantity' => 1,
            'unit_price' => $variant->price,
            'subtotal' => $variant->price
        ]);

        $this->command->info('Đã bơm xong 2 đơn hàng test cực xịn vào Database!');
    }
}