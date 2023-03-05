<?php

namespace App\Cih\Commands\Autogenerate;

use App\Cih\Commands\Autogenerate\Traits\CommonTrait;
use App\Cih\Commands\Autogenerate\Traits\ModelTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Migration extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autogenerate:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a migration';


    /**
     * Autogenerate common methods
     */
    use CommonTrait;

    use ModelTrait;

    /**
     * Execute AutoGenerate Task.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->autoGenerateProps = session('autoGenerateProps') ?? new AutoGenerateProps();

        return $this->createMigration();
    }

    /**
     * @return array
     */
    protected function createMigration($is_create_model = false)
    {

        $this->autoGenerateProps->mutations = [];

        if ($is_create_model === false) {
            $this->alert('Creating a migration');
            $proceed = $this->promptModel(true, false);
            if ($proceed === false) return false;

        }

        $this->getModelFields(false);

        // Handle Confirm
        $confirmed = false;
        while ($confirmed === false) {

            $fields = $this->autoGenerateProps->fields;
            $key = 0;
            $str = rtrim(array_reduce(
                $fields,
                function ($prev, $curr) use (&$key) {
                    $key++;
                    return $prev . $curr['name'] . '[' . $curr['type'] . '], ';
                },
                ''
            ), ', ');

            $confirmed = $this->confirm("You are about to create migration for " . $this->autoGenerateProps->model_name . " Model. \n These are the fields: " . $str . "\nIs everything alright?");
            if ($confirmed === false) {
                $this->createMigration();
            }
        }

        $migration_name = 'create_' . strtolower(Str::plural($this->autoGenerateProps->model_name)) . '_table';
        $migration_dir = base_path('database/migrations');
        $migrations = scandir($migration_dir);

        // array reverse for time complexity optimization
        foreach (array_reverse($migrations) as $migration) {
            if (preg_match("#" . $migration_name . "#", $migration)) return $this->error("Whoops! It seems like a similar migration already exists.");
        }

        $this->call('make:migration', ['name' => $migration_name]);

        $migrations = scandir($migration_dir);
        $migration = $migration_dir . '/' . $migrations[count($migrations) - 1];
        $migration_contents = file_get_contents($migration);
        $migration_arr = explode('$table->id();' . "\n", $migration_contents);
        $pre_migration_content = $migration_arr[0];
        $after_migration_content = $migration_arr[1];
        $current_migration_content = '$table->id();' . "\n";
        $fields = $this->autoGenerateProps->fields;

        foreach ($fields as $field) {
            $current_migration_content .= "\t\t\t" . '$table->' . $field['type'] . '(\'' . $field['name'] . '\');' . "\n";
        }
        $new_migration_content = $pre_migration_content . $current_migration_content . $after_migration_content;
        file_put_contents($migration, $new_migration_content);

        $this->autoGenerateProps->recently_created_migration = true;
        $this->autoGenerateProps->save($this->autoGenerateProps);
    }
}
