<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produtc extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'price',
        'discount',
        'amount',
        'image',
        'is_active',
        'is_avaliable'
    ];


    public function category(){
        return $this->belongsTo(Category::class,'categoty_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

}
