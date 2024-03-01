<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-config {module} {name=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new config file for a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $name = $this->argument('name');
        $configPath = base_path("modules/{$module}/Config/{$name}.php");

        if (file_exists($configPath)) {
            $this->error("The config file already exists!");
            return;
        }

        // Ensure the module's Config directory exists
        $this->makeDirectory(dirname($configPath));

        // Create the config file from the stub
        $stub = file_get_contents(app_path('Console/Stubs/config.stub'));
        file_put_contents($configPath, $stub);

        $this->info("Config file created successfully for module {$module}.");
    }

    protected function makeDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
            $this->info("Created directory: {$path}");
        }
    }
}
