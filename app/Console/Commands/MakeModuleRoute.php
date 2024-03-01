<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-routes {name} {module}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API and web route files for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $moduleNameLower = strtolower($this->argument('name'));
        $routesDirectory = base_path("Modules/{$module}/Routes");

        if (!is_dir($routesDirectory)) {
            mkdir($routesDirectory, 0777, true);
        }

        $this->createRouteFile('api', $module, $moduleNameLower, $routesDirectory);
        $this->createRouteFile('web', $module, $moduleNameLower, $routesDirectory);

        $this->info("Route files created successfully in module {$module}.");
    }

    protected function createRouteFile($type, $module, $moduleNameLower, $routesDirectory)
    {
        $stubPath = app_path("Console/Stubs/{$type}.stub");
        if (!file_exists($stubPath)) {
            $this->error("The stub file does not exist: {$stubPath}");
            return;
        }

        $stub = file_get_contents($stubPath);
        $stub = str_replace(['{{moduleName}}', '{{moduleNameLower}}'], [$module, $moduleNameLower], $stub);

        $filePath = "{$routesDirectory}/{$type}.php";
        if (file_exists($filePath)) {
            $this->error("The {$type} route file already exists for module {$module}!");
            return;
        }

        file_put_contents($filePath, $stub);
    }
}
