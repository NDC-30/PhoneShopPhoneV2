<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $table = 'attribute_values';
    protected $primaryKey = 'value_id';
    public $timestamps = false;

    protected $fillable = ['attribute_id', 'value', 'extra_price', 'image', 'sort_order'];

    public function attribute() {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'attribute_id');
    }

    public function variants() {
        return $this->belongsToMany(Variant::class, 'variant_attributes', 'value_id', 'variant_id');
    }
}