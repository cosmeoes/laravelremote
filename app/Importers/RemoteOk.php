<?php


namespace App\Importers;


use App\Models\JobPost;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class RemoteOk
{
    protected $baseUrl = "https://remoteok.com/api?tag=laravel";

    public function import()
    {
        $jobs = collect(Http::get($this->baseUrl)->json())->skip(1)->map(function ($job) {
            $body = Http::get($job['url'])->body();
            $scarped = $this->scrapeJobData($body);
            sleep(0.5);

            return array_merge($scarped, [
                'position' => $job['position'],
                'location' => $job['location'],
                'company' => $job['company'],
                'source_name' => Sources::$REMOTE_OK,
                'source_url' => $job['url'],
                'apply_url' => $job['url'],
                'source_created_at' => Carbon::createFromTimestamp($job['epoch'])
            ]);
        });

        JobPost::storeFromSource($jobs->toArray());
    }

    public function scrapeJobData($body)
    {
        $jsonData = $this->asJson($body);
        return array_merge(
            $this->salaryRange($jsonData, $body),
            ['job_type' => $jsonData['employmentType']]
        );
    }

    public function asJson($body)
    {
        preg_match('%<script type="application/ld\+json">({.+"@type":"JobPosting"[^<]+})</script>%s', $body, $match);
        return json_decode($match[1], true);
    }

    public function salaryRange($jobData, $body)
    {
        if (str_contains($body, 'No salary data published by company so we estimated salary based on similar jobs related to')) {
            return [];
        }

        return [
            'salary_currency' => Arr::get($jobData, 'baseSalary.currency'),
            'salary_max' => Arr::get($jobData, 'baseSalary.value.minValue'),
            'salary_min' => Arr::get($jobData, 'baseSalary.value.maxValue'),
        ];
    }
}
