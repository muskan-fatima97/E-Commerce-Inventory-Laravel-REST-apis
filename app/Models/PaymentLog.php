<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = ['order_id', 'type', 'payload', 'hash_valid'];

    protected $casts = [
        'payload' => 'array',
        'hash_valid' => 'boolean',
    ];
}