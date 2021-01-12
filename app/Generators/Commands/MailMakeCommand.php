<?php

namespace app\Generators\Commands;

use Illuminate\Console\GeneratorCommand;

class MailMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.Mail
     *
     * @var string
     */
    protected $name = 'make:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new email class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Mail';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return 'app/Generators/Stubs/mail/mail.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Mails';
    }
}
