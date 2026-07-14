<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
    'sizes' => 'array',
    // 'price' => 'decimal:2', 
];

    protected $fillable=['name','description','price', 'category', 'brand', 'sizes', 'color','stock_quantity','image'];
}
