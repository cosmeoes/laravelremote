<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeImporter extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function getStub()
    {
        return app_path() . '/Console/Commands/Stubs/make-report.stub';
    } 

    public function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Reports';
    }

    public function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace("DummyReport", $this->argument('name'), $stub);
    }
}
