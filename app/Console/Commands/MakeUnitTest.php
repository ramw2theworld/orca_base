<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeUnitTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-unit-test {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new unit test class for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $path = $this->getPath($name, $module);

        // Ensure the directory exists
        if (!is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true); // Correctly create directories recursively
        }

        // Fetch stub content
        $stub = file_get_contents($this->getStub());

        // Replace placeholders in the stub
        $stub = str_replace(
            ['{{ module }}', '{{ class }}'],
            [$module, $name],
            $stub
        );

        // Create the test file
        file_put_contents($path, $stub);

        $this->info("Unit test {$name} created successfully for the {$module} module.");
    }


    protected function getStub()
    {
        return app_path('Console/Stubs/unit-test.stub');
    }

    protected function getPath($name, $module)
    {
        return base_path("modules/{$module}/Tests/Unit/{$name}.php");
    }
}
