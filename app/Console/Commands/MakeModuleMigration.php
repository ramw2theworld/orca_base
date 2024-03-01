<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MakeModuleMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-migration {tableName} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = $this->argument('tableName');
        $module = $this->argument('module');
        $datePrefix = now()->format('Y_m_d_His');

        // Convert table name to lowercase plural form
        $tableNamePlural = Str::plural(strtolower($tableName));

        $migrationPath = base_path("Modules/{$module}/Database/migrations/{$datePrefix}_{$tableNamePlural}_table.php");

        // Ensure the migrations directory exists
        if (!is_dir($dirPath = dirname($migrationPath))) {
            mkdir($dirPath, 0777, true);
        }

        $stubPath = app_path('Console/Stubs/alter_table.stub');
        if (!file_exists($stubPath)) {
            $this->error("The stub file does not exist: {$stubPath}");
            return;
        }

        $stub = file_get_contents($stubPath);
        $content = str_replace('{{tableName}}', $tableNamePlural, $stub);

        if (!file_exists($migrationPath)) {
            file_put_contents($migrationPath, $content);
            $this->info("Migration to alter table '{$tableNamePlural}' created at: {$migrationPath}");
        } else {
            $this->error("A migration to alter table '{$tableNamePlural}' already exists.");
        }
    }
}
