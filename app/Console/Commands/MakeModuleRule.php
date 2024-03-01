<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-rule {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Rule for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $modelPath = base_path("Modules/{$module}/Rules/{$name}.php");

        if (file_exists($modelPath)) {
            $this->error("The rule already exists!");
            return;
        }

        if (!is_dir($dir = dirname($modelPath))) {
            mkdir($dir, 0777, true);
        }

        $namespace = "Modules\\{$module}\Rules";

        $stubPath = app_path('Console/Stubs/rule.stub');
        if (!file_exists($stubPath)) {
            $this->error("The stub file does not exist: {$stubPath}");
            return;
        }

        $stub = file_get_contents($stubPath);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $name],
            $stub
        );

        file_put_contents($modelPath, $stub);
        $this->info("Rule {$name} created successfully in module {$module}.");
    }
}
