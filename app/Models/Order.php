<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'order_id', 'order_id');
    }

    // ===== Bổ sung cho phần khách =====
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }
    protected $casts = [
    'created_at' => 'datetime',
];
}
