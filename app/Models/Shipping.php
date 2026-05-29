<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $primaryKey = 'shipping_id';
    
    protected $guarded = [];
    
    public $timestamps = false; 
}