<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModuleTableAlterMigration extends Command
{
    protected $signature = 'module:make-alter-migration {tableName} {module}';
    protected $description = 'Create a new migration file to alter an existing table for a specific module';

    public function handle()
    {
        $tableName = $this->argument('tableName');
        $module = $this->argument('module');
        $datePrefix = now()->format('Y_m_d_His');
        $migrationPath = base_path("Modules/{$module}/Database/migrations/{$datePrefix}_alter_{$tableName}_table.php");

        // Ensure the migrations directory exists
        if (!is_dir($dirPath = dirname($migrationPath))) {
            mkdir($dirPath, 0777, true);
        }

        $stubPath = app_path('Console/Stubs/alter_table.stub');
        $stub = file_get_contents($stubPath);
        $content = str_replace('{{tableName}}', $tableName, $stub);

        if (!file_exists($migrationPath)) {
            file_put_contents($migrationPath, $content);
            $this->info("Migration to alter table '{$tableName}' created at: {$migrationPath}");
        } else {
            $this->error("A migration to alter table '{$tableName}' already exists.");
        }
    }
}
