<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-resource {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource for a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');

        $path = base_path("modules/{$module}/Http/Resources/{$name}.php");

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Get the stub content and replace the placeholders.
        $stub = file_get_contents(app_path('Console/Stubs/resource.stub'));
        $stub = str_replace('DummyClass', $name, $stub);
        $stub = str_replace('DummyModule', $module, $stub);

        // Write the new resource file.
        file_put_contents($path, $stub);

        $this->info("Resource {$name} created successfully.");
    }
}
