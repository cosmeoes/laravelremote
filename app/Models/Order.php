<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sticky' => 'boolean',
        'paid' => 'boolean'
    ];

    public static function makeOrder()
    {

    }
}
