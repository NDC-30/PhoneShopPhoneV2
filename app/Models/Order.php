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
}