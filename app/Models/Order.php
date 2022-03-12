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

    public function isSticky()
    {
        return $this->sticky;
    }

    public function hasColorHighlight()
    {
        return $this->color != null;
    }

    public function hasCompanyLogo()
    {
        return $this->logo_path != null;
    }
}

