<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    protected $casts = [
        'source_created_at' => 'datetime'
    ];

    protected $guarded = [];

    public static function storeFromSource($jobs)
    {
        foreach ($jobs as $job) {
            if (JobPost::where('source_url', $job['source_url'])->orWhere('apply_url', $job['apply_url'])->doesntExist()) {
                self::create($job);
            }
        }
    }
}
