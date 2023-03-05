<?php

namespace App\Cih\Commands\Autogenerate;

use App\Cih\Commands\Autogenerate\Traits\CommonTrait;
use App\Cih\Commands\Autogenerate\Traits\CreateRouteOrViewTrait;
use Illuminate\Console\Command;

class View extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autogenerate:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a view';


    /**
     * Autogenerate common methods
     */
    use CommonTrait;

    use CreateRouteOrViewTrait;

    /**
     * Execute AutoGenerate Task.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->autoGenerateProps = session('autoGenerateProps') ?? new AutoGenerateProps();

        return $this->createView();
    }

    public function createView()
    {
        $this->autoGenerateProps->mutations = [];

        $this->alertUser('Creating a view');

        $path = $this->createRouteOrView(true);
        if ($path === false) return false;

        // $this->setPath(preg_replace("#" . strtolower($this->autoGenerateProps->default_name . '/' . $this->autoGenerateProps->default_name) . "$#", strtolower($this->autoGenerateProps->default_name), $path));
        $path = base_path("resources/views/" . $path . '.blade.php');

        if (file_exists($path)) return $this->error("Whoops! It seems like a similar view already exists.");

        if (strtolower($this->ask("Create view with content from model? Y or N", 'Yes')) == 'y') {
            if (!$this->autoGenerateProps->model_name)
                $this->getModelFields();
            $content = file_get_contents(base_path("app/Cih/templates/view.txt"));
        } else {
            $this->autoGenerateProps->set('view_title', $this->ask("What is the view title?"));
            $content = file_get_contents(base_path("app/Cih/templates/empty_view.txt"));
        }

        $new_content = $this->replaceVars($content);
        $this->storeFile($path, $new_content);

        $this->autoGenerateProps->save($this->autoGenerateProps);
    }
}
