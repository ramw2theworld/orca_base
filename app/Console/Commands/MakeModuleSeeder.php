<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-seeder {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $seederPath = base_path("Modules/{$module}/Database/seeders/{$name}.php");

        $directory = dirname($seederPath);
        if (!is_dir($directory)) {
            // Create the directory with recursive mkdir
            mkdir($directory, 0777, true);
        }

        $stub = file_get_contents(app_path('Console/Stubs/seeder.stub'));
        $stub = str_replace(['{{moduleName}}', '{{className}}'], [$module, $name], $stub);

        if (!file_exists($seederPath)) {
            file_put_contents($seederPath, $stub);
            $this->info("Seeder {$name} created successfully in module {$module}.");
        } else {
            $this->error("The seeder {$name} already exists.");
        }
    }
}
