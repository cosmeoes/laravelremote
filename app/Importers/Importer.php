<?php

namespace App\Importers;

use App\Helpers\PersistFromSource;

abstract class Importer
{
    protected $persistFromSource;

    public function __construct(PersistFromSource $persistFromSource) 
    {
        $this->persistFromSource = $persistFromSource;
    }

    public function run()
    {
        $this->persistFromSource->do($this->import());
    }

    abstract function import();
}
