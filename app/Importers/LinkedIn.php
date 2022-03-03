<?php

namespace App\Importers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class LinkedIn extends Importer
{
    public $baseUrl = "https://www.linkedin.com/jobs/search/?keywords=Laravel&f_TPR=r86400&f_WT=2";
    public $host = "https://www.linkedin.com";
    public $userAgent = "Mozilla/5.0 (X11; Linux x86_64; rv:96.0) Gecko/20100101 Firefox/96.0";

    public function import()
    {
        return collect($this->urls())->map(function ($url) {
            $urlData = parse_url($url);
            $url = $this->host . $urlData['path'];
            try {
                $body = Http::withUserAgent($this->userAgent)->get($url)->body();
                sleep(0.5);
                $json = $this->asJson($body);
            } catch (Exception $e) {
                return null;
            }

            return [
                'position' => $json['title'],
                'location' => null,
                'job_type' => $json['employmentType'],
                'company' => Arr::get($json, 'hiringOrganization.name'),
                'body' => html_entity_decode($json['description']),
                'salary_max' => Arr::get($json, 'baseSalary.value.maxValue', 0),
                'salary_min' => Arr::get($json, 'baseSalary.value.minValue', 0),
                'salary_currency' => Arr::get($json, 'baseSalary.currency'),
                'salary_unit' => Arr::get($json, 'baseSalary.value.unitText'),
                'source_name' => Sources::$LINKEDIN,
                'source_url' => $url,
                'apply_url' => $url,
                'source_created_at' => Carbon::createFromTimeString($json['datePosted'])
            ];
        })->filter();
    }

    public function urls()
    {
        $body = Http::withUserAgent($this->userAgent)->get($this->baseUrl)->body();
        preg_match_all('@<a[^>]+class="base-card__full-link"[^>]+href="([^"?]+)@', $body, $match);
        return $match[1];
    }

    public function asJson($body)
    {
        preg_match('%<script type="application/ld\+json">\s*({.+"@type":"JobPosting"[^<]+})\s*</script>%', $body, $match);
        return json_decode($match[1], true);
    }
}

