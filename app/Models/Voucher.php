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

    // ===== Bổ sung cho phần khách =====
    public function isUsable(): bool
    {
        if ((int) $this->status !== 1) return false;
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) return false;
        if ($this->end_date && $now->gt($this->end_date)) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calcDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percent') {
            $d = $subtotal * ($this->discount_value / 100);
            if ($this->max_discount) $d = min($d, (float) $this->max_discount);
        } else {
            $d = (float) $this->discount_value;
        }
        return min($d, $subtotal);
    }
}
