<?php

namespace App\Importers;

use Illuminate\Support\Facades\Http;

class LinkedIn
{
    public $baseUrl = "https://www.linkedin.com/jobs/search/?f_WT=2&geoId=92000000&keywords=laravel";

    public function import()
    {
        dd(Http::get($this->baseUrl)->body());
    }
}

