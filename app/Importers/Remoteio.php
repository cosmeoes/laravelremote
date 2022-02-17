<?php


namespace App\Importers;


use App\Models\JobPost;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Remoteio
{
    public $baseUrl = "https://www.remote.io";

    public function import()
    {
        $jobs = $this->postURLs()->map(function ($url) {
            $sourceUrl = $this->baseUrl . '/' . ltrim( $url, '\\');
            $body = Http::get($sourceUrl)->body();
            sleep(0.5);

            return $this->parseBody($body, $sourceUrl);
        });

        JobPost::storeFromSource($jobs);
    }

    public function postURLs()
    {
        $body = Http::get($this->baseUrl . '/remote-jobs-to-work-from-home?s=laravel')->body();

        preg_match_all("/window.location='(.+)'/", $body, $matches);

        return collect($matches[1]);
    }


    public function parseBody($body, $sourceUrl)
    {
        $json = $this->asJson($body);
        $salary = $this->salary($json, $body);

        return array_merge($salary, [
            'position' => $json['title'],
            'location' => $json['location'],
            'job_type' => strtolower(str_replace("_", " ", $json['employmentType'])),
            'company' => Arr::get($json, 'hiringOrganization.name'),
            'body' => $json['description'],
            'source_name' => Sources::$REMOTE_IO,
            'source_url' => $sourceUrl,
            'apply_url' => $sourceUrl,
            'source_created_at' => Carbon::createFromTimeString($json['datePosted'])
        ]);
    }


    public function asJson($body)
    {
        preg_match('%<script type="application/ld\+json">({.+"@type":"JobPosting".+?})</script>%s', $body, $match);
        $json = json_decode($match[1], true);

        $json['location'] = $this->location($body);
        return $json;
    }

    public function location($body)
    {
        preg_match('%<li>\s+<p[^>]+>\s*location or timezone\s*</p>\s*<div[^>]+>\s*<span[^>]+>\s*(?:<a[^>]+>\s*<img[^>]+>)?([^<]+)(?:</a>\s*)?</span>%s', $body, $location);
        return $location[1];
    }

    public function salary($json, $body)
    {
        if (!isset($json['baseSalary'])) {
            return [];
        }

        preg_match("%<li>\s*<p[^>]+>[^<]+salary range</p>\s*<div[^?]+>([^-]+)-[^<]+</div>%", $body, $minSalary);
        $minSalary = str_replace(['$', ','], ['', ''], trim($minSalary[1]));

        if (!((string)(int)$minSalary) == $minSalary) {
           $minSalary = 0;
        }

        return [
            'salary_currency' => Arr::get($json, 'baseSalary.currency'),
            'salary_max' => Arr::get($json, 'baseSalary.value.value', 0),
            'salary_min' => $minSalary,
            'salary_unit' => Arr::get($json, 'baseSalary.value.unitText')
        ];
    }
}
