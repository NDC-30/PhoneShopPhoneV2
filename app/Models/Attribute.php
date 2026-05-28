<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';
    protected $primaryKey = 'attribute_id';
    public $timestamps = false;

    protected $fillable = ['name', 'display_name', 'filterable', 'sort_order'];

    // 1 Thuộc tính (RAM) có nhiều Giá trị (8GB, 16GB)
    public function values() {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'attribute_id');
    }
}