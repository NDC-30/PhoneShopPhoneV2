<?php

return [
    // 2 thông tin VNPay cấp khi đăng ký sandbox tại https://sandbox.vnpayment.vn/devreg
    'tmn_code'    => env('VNP_TMNCODE', ''),
    'hash_secret' => env('VNP_HASHSECRET', ''),

    // URL cổng thanh toán sandbox (không cần đổi)
    'url'         => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
];
