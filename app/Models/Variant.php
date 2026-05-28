<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variants';
    protected $primaryKey = 'variant_id';
    
    const UPDATED_AT = null; 

    protected $fillable = ['product_id', 'sku', 'price', 'compare_price', 'cost', 'stock', 'sold', 'image', 'weight', 'status'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Kết nối EAV qua bảng attribute_values (N-N)
    public function attributeValues() {
        return $this->belongsToMany(AttributeValue::class, 'variant_attributes', 'variant_id', 'value_id');
    }

    public function images() {
        return $this->hasMany(VariantImage::class, 'variant_id', 'variant_id');
    }
}