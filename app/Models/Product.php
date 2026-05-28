<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    
    public $timestamps = true;

    protected $fillable = ['name', 'slug', 'description', 'brand_id', 'category_id', 'is_featured', 'status'];

    // 1 Sản phẩm có nhiều Biến thể
    public function variants() {
        return $this->hasMany(Variant::class, 'product_id', 'product_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    // Lấy tất cả ảnh chung của sản phẩm
    public function images() {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }
}