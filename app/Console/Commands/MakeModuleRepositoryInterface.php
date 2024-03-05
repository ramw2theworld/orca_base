<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleRepositoryInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-repo-interface {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository interface for a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->argument('module');
        $interfacePath = base_path("modules/{$module}/Repositories/Contracts/{$name}Interface.php");
        $repositoryPath = base_path("modules/{$module}/Repositories/Eloquent/{$name}Repository.php");

        if (file_exists($interfacePath) || file_exists($repositoryPath)) {
            $this->error('The repository interface or implementation already exists!');
            return;
        }

        $this->makeDirectory($interfacePath);
        $this->makeDirectory($repositoryPath);

        $this->createInterface($name, $module, $interfacePath);
        $this->createRepository($name, $module, $repositoryPath);

        $this->info("Repository interface and implementation created successfully for module {$module}.");
    }

    protected function makeDirectory($path)
    {
        if (!is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }
    }

    protected function createInterface($name, $module, $path)
    {
        $stub = file_get_contents(app_path('Console/Stubs/repository-interface.stub'));
        $interfaceContent = str_replace(
            ['{{ moduleName }}', '{{ interfaceName }}'],
            [$module, $name . 'RepositoryInterface'],
            $stub
        );
        file_put_contents($path, $interfaceContent);
    }

    protected function createRepository($name, $module, $path)
    {
        $stub = file_get_contents(app_path('Console/Stubs/repository.stub'));
        $content = str_replace(
            ['{{ moduleName }}', '{{ interfaceName }}', '{{ className }}'],
            [$module, $name . 'RepositoryInterface', $name . 'Repository'],
            $stub
        );

        file_put_contents($path, $content);
    }
}
