<?php

namespace App\Cih\Commands\Autogenerate;

use App\Cih\Commands\Autogenerate\Traits\CommonTrait;
use App\Cih\Commands\Autogenerate\Traits\CreateRouteOrViewTrait;
use Illuminate\Console\Command;

class Route extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autogenerate:route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a route';

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

        return $this->createRoute();
    }

    public function createRoute()
    {
        $this->autoGenerateProps->mutations = [];

        $this->alertUser('Creating a route');

        $path = $this->createRouteOrView();
        if ($path === false) return false;

        $this->setPath(preg_replace("#" . strtolower($this->autoGenerateProps->default_name . '/' . $this->autoGenerateProps->default_name) . "$#", strtolower($this->autoGenerateProps->default_name), $path));
        $path = base_path("routes/" . $path . '.route.php');

        if (file_exists($path)) return $this->error("Whoops! It seems like a similar route already exists.");

        $content = file_get_contents(base_path("app/Cih/templates/route.txt"));

        $new_content = $this->replaceVars($content, true);
        $this->storeFile($path, $new_content);
        
        $this->autoGenerateProps->save($this->autoGenerateProps);
    }
}
