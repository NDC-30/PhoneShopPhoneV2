<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    protected $primaryKey = 'category_id'; 
    
    public $timestamps = false; 

    protected $fillable = [
        'name', 
        'slug', 
        'parent_id', 
        'sort_order', 
        'status'
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}