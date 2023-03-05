<?php

namespace App\Cih\Commands\Autogenerate;

use App\Cih\Commands\Autogenerate\Traits\CommonTrait;
use App\Cih\Commands\Autogenerate\Traits\ModelTrait;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class Model extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autogenerate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a model';

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

        return $this->createModel();
    }

    public function createModel()
    {
        $this->autoGenerateProps->mutations = [];

        $this->alertUser('Creating a model');

        $proceed = $this->promptModel(true);
        if ($proceed === false) return false;

        $this->getModelFields(true);

        $confirmed = false;
        while ($confirmed === false) {
            $confirmed = $this->confirm("The Model path will be: app/Models" .  $this->autoGenerateProps->model_folder . '/' . $this->autoGenerateProps->model_name . '.php' . "\nIs everything alright?");
            if ($confirmed === false) {
                $this->createModel(false);
            };
        }

        $path_init = $this->autoGenerateProps->model_folder . '/' . $this->autoGenerateProps->model_name;
     
        $path = app_path('Models/' . $path_init . '.php');

        if (file_exists($path)) return $this->error("Whoops! It seems like a similar Model already exists.");

        Artisan::call("make:model", [
            'name' => $path_init
        ]);

        $model_content = file_get_contents($path);
        $model_array = explode('}', $model_content);

        $pre_model_content = $model_array[0];
        $post_model_content = $model_array[1] . '}';
        $this->autoGenerateProps->model_fields = '"' . implode('", "', $this->autoGenerateProps->plain_fields) . '"';
        $current_model_content = "\n\t" . 'protected $fillable = [' . $this->autoGenerateProps->model_fields . '];' . "\n";
        $new_model_contents = $pre_model_content . $current_model_content . $post_model_content;
        file_put_contents($path, $new_model_contents);

        $this->autoGenerateProps->recently_created_model = true;
        $this->autoGenerateProps->save($this->autoGenerateProps);

    }
}
