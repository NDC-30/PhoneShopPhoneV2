<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $primaryKey = 'voucher_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'code', 
        'name',
        'discount_type', 
        'discount_value', 
        'min_order_value', 
        'max_discount',
        'usage_limit', 
        'status'
    ];

    protected $attributes = [
        'status' => 1,
        'discount_type' => 'fixed',
    ];
}