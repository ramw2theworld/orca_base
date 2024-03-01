<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-event {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event class for module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $modelPath = base_path("Modules/{$module}/Events/{$name}.php");

        if (file_exists($modelPath)) {
            $this->error("The event already exists!");
            return;
        }

        if (!is_dir($dir = dirname($modelPath))) {
            mkdir($dir, 0777, true);
        }

        $namespace = "Modules\\{$module}\Events";

        $stubPath = app_path('Console/Stubs/event.stub');
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
        $this->info("Event {$name} created successfully in module {$module}.");
    }
}
