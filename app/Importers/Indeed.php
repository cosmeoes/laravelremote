<?php

namespace App\Importers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Indeed extends Importer
{
    protected $baseUrl = 'https://www.indeed.com';
    public $userAgent = "Mozilla/5.0 (X11; Linux x86_64; rv:96.0) Gecko/20100101 Firefox/96.0";

    public function import()
    {
        return $this->jobList()->map(function($job)  {
            $sourceUrl = $this->baseUrl . $job['link'];
            try {
                $scarpedData = $this->scrapeData($sourceUrl);
            } catch (Exception $e) {
                Log::error("Indeed importer job $sourceUrl failed with error: \n". $e->getMessage() . ":\n " . $e->getTraceAsString());
                return false;
            }

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
                'source_created_at' => $job['formattedRelativeTime'] == 'Just posted' ? Carbon::now()->addMinutes(30) : Carbon::createFromTimestamp(strtotime($job['formattedRelativeTime']))
            ]);
        })->reject(fn($job) => $job == false);
    }

    public function jobList()
    {
        $body = Http::withUserAgent($this->userAgent)->get($this->baseUrl . "/jobs?q=laravel&l=Remote&sort=date")->body();
        preg_match('@window\.mosaic\.providerData\["mosaic\-provider\-jobcards"\]=({.+})@', $body, $matches);
        if (! isset($matches[1])) {
            dd($body);
            dump($matches);
            dd($this->baseUrl . "/jobs?q=laravel&l=Remote&sort=date");
        }
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
        $body = Http::withUserAgent($this->userAgent)->get($sourceUrl)->body();
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
