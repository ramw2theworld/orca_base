<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-request {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new request for form validation in a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');

        $path = base_path("modules/{$module}/Http/Requests/{$name}.php");

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $stub = file_get_contents(app_path('Console/Stubs/request.stub'));
        $stub = str_replace('DummyClass', $name, $stub);
        $stub = str_replace('DummyModule', $module, $stub);

        file_put_contents($path, $stub);

        $this->info("Request {$name} created successfully.");
    }

}
