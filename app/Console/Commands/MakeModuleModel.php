<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-model {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class in a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $modelPath = base_path("Modules/{$module}/Models/{$name}.php");

        if (file_exists($modelPath)) {
            $this->error("The model already exists!");
            return;
        }

        if (!is_dir($dir = dirname($modelPath))) {
            mkdir($dir, 0777, true);
        }

        $namespace = "Modules\\{$module}\Models";

        $stubPath = app_path('Console/Stubs/model.stub');
        if (!file_exists($stubPath)) {
            $this->error("The stub file does not exist: {$stubPath}");
            return;
        }

        $stub = file_get_contents($stubPath);
        $stub = str_replace(
            ['{{namespace}}', '{{className}}'],
            [$namespace, $name],
            $stub
        );

        file_put_contents($modelPath, $stub);
        $this->info("Model {$name} created successfully in module {$module}.");
    }

}
