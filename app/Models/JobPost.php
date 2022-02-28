<?php

namespace App\Models;

use App\Casts\SalaryRangeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    protected $casts = [
        'source_created_at' => 'datetime',
        'salary_range' => SalaryRangeCast::class
    ];

    protected $guarded = [];

    public static function storeFromSource($jobs)
    {
        foreach ($jobs as $job) {
            if (self::shouldAddJob($job)) {
                self::create($job);
            }
        }
    }

    public static function shouldAddJob($job)
    {
        return JobPost::where('source_url', $job['source_url'])->orWhere('apply_url', $job['apply_url'])->orWhere(function($query) use ($job) {
            $query->where('position', $job->position)
                  ->where('company', $job->company)
                  ->where('created_at', '>=', now()->addWeek());
        })->doesntExist();
    }
}
