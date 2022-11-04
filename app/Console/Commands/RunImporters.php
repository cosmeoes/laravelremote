<?php

namespace App\Console\Commands;

use App\Importers\Indeed;
use App\Importers\LinkedIn;
use App\Importers\Remoteio;
use App\Importers\RemoteOk;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunImporters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lr:run-importers {--only=all : comma separated classnames (without namespace)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the job importers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $importers = $this->importers();

        Log::info("Running ". count($importers). " Importers");


        foreach ($importers as $class) {
            try {
                Log::info("Running " . $class);
                app($class)->run();
            } catch (Exception $e) {
                Log::error("Importer " . $class . " failed with error: \n". $e->getMessage() . ":\n " . $e->getTraceAsString());
            }
        }

        Log::info("Importing finished");
        return 0;
    }

    public function importers()
    {
        if ($this->option('only') == 'all') {
            return [
                RemoteOk::class,
                Remoteio::class,
//                Indeed::class, added cloudflare
                LinkedIn::class,
            ];
        }

        $classNames = explode(',', $this->option('only'));

        $importers = [];
        foreach ($classNames as $className) {
            $namespacedClass = '\App\Importers\\' . $className;
            if (class_exists($namespacedClass)) {
                $importers[] = $namespacedClass;
            } else {
                Log::warning("Class with name $namespacedClass not found");
                exit(1);
            }
        }

        return $importers;
    }
}
