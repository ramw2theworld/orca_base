<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleMiddleware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-middleware {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new middleware class in a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');

        // Define the path where you want to generate the middleware.
        $path = base_path("modules/{$module}/Http/Middleware/{$name}.php");

        // Ensure the directory exists.
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Get the stub content and replace the placeholders.
        $stub = file_get_contents(app_path('Console/Stubs/middleware.stub'));
        $stub = str_replace(['DummyClass', 'DummyModule'], [$name, $module], $stub);

        // Write the new middleware file.
        file_put_contents($path, $stub);

        $this->info("Middleware {$name} created successfully in module {$module}.");
    }
}
