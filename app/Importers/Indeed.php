<?php


namespace App\Importers;


use Http;

class Indeed
{
    protected $baseUrl = 'https://www.indeed.com/jobs?q=laravel&l=Remote';

    public function import()
    {
        Http::get($this->baseUrl)->body();
    }
}
