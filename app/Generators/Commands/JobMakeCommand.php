<?php

namespace app\Generators\Commands;

use Illuminate\Console\GeneratorCommand;

class JobMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.Job
     *
     * @var string
     */
    protected $name = 'make:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return 'app/Generators/Stubs/job/job.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Jobs';
    }
}
