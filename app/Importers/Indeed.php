<?php

namespace App\Importers;

use App\Models\JobPost;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Indeed
{
    protected $baseUrl = 'https://www.indeed.com';

    public function import()
    {
        $jobs = $this->jobList()->map(function($job)  {
            $sourceUrl = $this->baseUrl . $job['link'];
            $scarpedData = $this->scrapeData($sourceUrl);

            return array_merge($scarpedData, [
                'position' => $job['displayTitle'],
                'location' => null,
                'job_type' => $this->jobTypes($job),
                'company' => $job['company'],
                'salary_max' => Arr::get($job, 'extractedSalary.max', 0),
                'salary_min' => Arr::get($job, 'extractedSalary.min', 0),
                'salary_unit' => Arr::get($job, 'extractedSalary.type'),
                'salary_currency' => 'USD',
                'source_name' => Sources::$INDEED,
                'source_url' => $sourceUrl,
                'apply_url' => $sourceUrl,
                'source_created_at' => Carbon::createFromTimestamp(strtotime($job['formattedRelativeTime']))
            ]);
        });

        JobPost::storeFromSource($jobs);
    }

    public function jobList()
    {
        $body = Http::get($this->baseUrl . "/jobs?q=laravel&l=Remote&sort=date")->body();
        preg_match('@window\.mosaic\.providerData\["mosaic\-provider\-jobcards"\]=({.+})@', $body, $matches);
        return collect(Arr::get(json_decode($matches[1], true), 'metaData.mosaicProviderJobCardsModel.results'));
    }

    public function jobTypes($job)
    {
        $types = collect($job['taxonomyAttributes'])->first(function ($item) {
            return $item['label'] == 'job-types';
        });

        if (!$types) {
            return "";
        }

        return strtolower(implode(', ', array_column($types['attributes'], 'label')));
    }

    public function scrapeData($sourceUrl)
    {
        $body = Http::get($sourceUrl)->body();
        sleep(0.5);
        $jsonData = $this->asJson($body);
        return [
            'body' => Arr::get($jsonData, 'jobInfoWrapperModel.jobInfoModel.sanitizedJobDescription.content')
        ];
    }

    public function asJson($body)
    {
        preg_match('@window\._initialData=({.+})@', $body, $matches);
        return json_decode($matches[1], true);
    }
}
