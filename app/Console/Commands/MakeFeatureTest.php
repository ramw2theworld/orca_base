<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeFeatureTest extends Command
{
    protected $signature = 'module:make-feature-test {name} {module}';
    protected $description = 'Create a new feature test class';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $path = $this->getPath($name, $module);

        if (!is_dir($dir = dirname($path))) {
            mkdir($dir, 0755, true);
        }

        $stub = file_get_contents($this->getStub());
        $stub = str_replace(['{{ module }}', '{{ class }}'], [$module, $name], $stub);
        file_put_contents($path, $stub);

        $this->info("Feature test created successfully for the {$module} module.");
    }

    protected function getStub()
    {
        return app_path('Console/stubs/feature-test.stub');
    }

    protected function getPath($name, $module)
    {
        return base_path("modules/{$module}/Tests/Feature/{$name}.php");
    }
}
