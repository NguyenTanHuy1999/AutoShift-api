<?php

namespace app\Generators\Commands;

use Illuminate\Console\GeneratorCommand;

class ScheduleMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.Schedule
     *
     * @var string
     */
    protected $name = 'make:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new schedule class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Schedule';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return 'app/Generators/Stubs/schedule/schedule.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Schedules';
    }
}
