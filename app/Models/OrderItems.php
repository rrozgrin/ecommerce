<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'quantity',
        'price',
        'product_id',
    ];

    public function order()
    {
        $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        $this->belongsTo(Order::class, 'product_id');
    }
}
