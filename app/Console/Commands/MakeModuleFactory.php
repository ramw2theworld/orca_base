<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-factory {model} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new factory for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->argument('model');
        $module = $this->argument('module');
        $factoryPath = base_path("Modules/{$module}/Database/factories/{$model}.php");

        $directory = dirname($factoryPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $stub = file_get_contents(app_path('Console/Stubs/factory.stub'));
        $stub = str_replace(['{{moduleName}}', '{{modelName}}'], [$module, $model], $stub);

        if (!file_exists($factoryPath)) {
            file_put_contents($factoryPath, $stub);
            $this->info("Factory {$model}Factory created successfully in module {$module}.");
        } else {
            $this->error("The factory {$model}Factory already exists.");
        }
    }
}
