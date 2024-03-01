<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleController extends Command
{
    // Define the command signature
    protected $signature = 'module:make-controller {name} {module}';
    protected $description = 'Create a new controller class in a specific module';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $controllerPath = base_path("Modules/{$module}/Http/Controllers/{$name}.php");

        // Check if controller already exists
        if (file_exists($controllerPath)) {
            $this->error("The controller already exists!");
            return false;
        }

        // Make directory if it doesn't exist
        if (!is_dir($dir = dirname($controllerPath))) {
            mkdir($dir, 0777, true);
        }

        $namespace = "Modules\\{$module}\Http\Controllers";

        // Load the stub and replace placeholders
        $stub = file_get_contents(app_path('Console/Stubs/controller.stub'));
        $content = str_replace(
            ['{{namespace}}', '{{className}}'],
            [$namespace, $name],
            $stub
        );

        // Create the controller file
        file_put_contents($controllerPath, $content);
        $this->info("Controller {$name} created successfully in module {$module}.");
    }
}
