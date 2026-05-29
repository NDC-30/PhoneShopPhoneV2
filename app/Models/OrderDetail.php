<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $primaryKey = 'order_detail_id';
    protected $guarded = [];
    public $timestamps = false; 

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'variant_id');
    }
}