<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    protected $table = 'variant_images';
    protected $primaryKey = 'image_id';
    public $timestamps = false;
    protected $guarded = [];
}
