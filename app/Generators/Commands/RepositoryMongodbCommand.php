<?php

namespace app\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Generators\MongoModelGenerator;
use Prettus\Repository\Generators\Commands\RepositoryCommand;
use Prettus\Repository\Generators\FileAlreadyExistsException;
use Prettus\Repository\Generators\RepositoryEloquentGenerator;
use Prettus\Repository\Generators\RepositoryInterfaceGenerator;

class RepositoryMongodbCommand extends RepositoryCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repomongo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new repository mongodb';

    /**
     * Execute the command.
     */
    public function fire()
    {
        $this->generators = new Collection();

        $modelGenerator = new MongoModelGenerator([
            'name' => $this->argument('name'),
            'fillable' => $this->option('fillable'),
            'force' => $this->option('force'),
        ]);

        $this->generators->push($modelGenerator);

        $this->generators->push(new RepositoryInterfaceGenerator([
            'name' => $this->argument('name'),
            'force' => $this->option('force'),
        ]));

        foreach ($this->generators as $generator) {
            $generator->run();
        }

        $model = $modelGenerator->getRootNamespace().'\\'.$modelGenerator->getName();
        $model = str_replace([
            '\\',
            '/',
        ], '\\', $model);

        try {
            (new RepositoryEloquentGenerator([
                'name' => $this->argument('name'),
                'rules' => $this->option('rules'),
                'validator' => $this->option('validator'),
                'force' => $this->option('force'),
                'model' => $model,
            ]))->run();

            if ($this->confirm('Would you like to create a Transformer? [y|N]')) {

                // Generate a transformer
                $this->call('make:transformer', [
                    'name' => $this->argument('name'),
                    '--force' => $this->option('force'),
                ]);
            }

            $this->info('MongoDB Repository created successfully.');
        } catch (FileAlreadyExistsException $e) {
            $this->error($this->type.' already exists!');

            return false;
        }
    }
}
