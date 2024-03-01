<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-provider {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $providerPath = base_path("Modules/{$module}/Providers/{$name}.php");

        // Ensure the Providers directory exists
        if (!is_dir($dirPath = dirname($providerPath))) {
            mkdir($dirPath, 0777, true);
        }

        $stub = file_get_contents(app_path('Console/Stubs/provider.stub'));
        $stub = str_replace(
            ['{{moduleName}}', '{{className}}'],
            [$module, $name],
            $stub
        );

        if (!file_exists($providerPath)) {
            file_put_contents($providerPath, $stub);
            $this->info("Service Provider {$name} created successfully in module {$module}.");
        } else {
            $this->error("The service provider {$name} already exists.");
        }
    }
}
