<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-listener {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new listener for a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $modelPath = base_path("Modules/{$module}/Listeners/{$name}.php");

        if (file_exists($modelPath)) {
            $this->error("The listener already exists!");
            return;
        }

        if (!is_dir($dir = dirname($modelPath))) {
            mkdir($dir, 0777, true);
        }

        $namespace = "Modules\\{$module}\Listeners";

        $stubPath = app_path('Console/Stubs/listener.stub');
        if (!file_exists($stubPath)) {
            $this->error("The stub file does not exist: {$stubPath}");
            return;
        }

        $stub = file_get_contents($stubPath);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $name],
            $stub
        );

        file_put_contents($modelPath, $stub);
        $this->info("Listener {$name} created successfully in module {$module}.");
    }
}
