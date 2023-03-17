<?php

namespace App\Cih\Commands\Autogenerate;

use Illuminate\Support\Facades\Artisan;
use App\Cih\Commands\Autogenerate\Traits\CommonTrait;
use App\Cih\Commands\Autogenerate\Traits\CreateRouteOrViewTrait;
use App\Cih\Commands\Autogenerate\Traits\ModelTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Controller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autogenerate:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a controller';

    /**
     * Autogenerate common methods
     */
    use CommonTrait;

    use CreateRouteOrViewTrait;

    use ModelTrait;

    /**
     * Execute AutoGenerate Task.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->autoGenerateProps = session('autoGenerateProps') ?? new AutoGenerateProps();

        return $this->createController();
    }

    public function createController($intro_message = true)
    {
        $this->autoGenerateProps->mutations = [];

        if ($intro_message) $this->alertUser('Creating a controller');

        $proceed = $this->promptControllerName();
        if ($proceed === false) return false;

        $proceed = $this->promptControllerFolder();
        if ($proceed === false) return false;

        $confirmed = false;
        while ($confirmed === false) {
            $confirmed = $this->confirm("The Controller path will be: app/Http/Controllers/" . $this->namespaceToPath(true) . '/' . $this->autoGenerateProps->controller_name . '.php' . "\nIs everything alright?");
            if ($confirmed === false) {
                $this->rollbackProps();
                $this->createController(false);
            };
        }

        if (!$this->autoGenerateProps->path)
            $this->autoGenerateProps->set('path', $this->namespaceToPath());

        $path_init = trim($this->namespaceToPath() . '/' . $this->autoGenerateProps->controller_name, '/');

        $path = app_path('Http/Controllers/' . $path_init . '.php');

        if (file_exists($path)) return $this->error("Whoops! It seems like a similar Controller already exists.");

        Artisan::call("make:controller", [
            'name' => $path_init
        ]);

        $content = file_get_contents($path);

        $new_content = file_get_contents(base_path("app/Cih/templates/controller.txt"));
        $new_content = $this->replaceVars($new_content);
        $new_controller = str_replace('//', $new_content, $content);
        $use_content = 'use ' . str_replace('/', '\\', $this->autoGenerateProps->model_namespace) . ";\nuse App\Repositories\SearchRepo;\nuse Illuminate\Support\Facades\Schema;\nuse App\Http\Traits\ControllerTrait;\n\nclass";

        $new_controller = $this->replace_first('class', $use_content, $new_controller);
        file_put_contents($path, $new_controller);

        $this->autoGenerateProps->save($this->autoGenerateProps);
    }

    public function setNamespace($namespace = null)
    {
        if ($namespace)
            $namespace = preg_replace("#\\\\#", '/', $namespace);
        else
            $namespace = $this->autoGenerateProps->controller_subfolder;

        $namespace = trim($namespace, '/');

        $parts = explode('/', $namespace);
        $this->autoGenerateProps->set('namespace', ltrim(preg_replace("#\\\\+#", "\\\\", array_reduce($parts, fn ($prev, $curr) => $prev . '\\' . Str::studly($curr), '')), '\\'));
    }

    public function namespace()
    {
        if (!$this->autoGenerateProps->fully_qualified_class) {
            $path = $this->autoGenerateProps->controller_folder . '/' . preg_replace('#' . $this->autoGenerateProps->controller_folder . '#i', '', $this->autoGenerateProps->controller_subfolder);
            $path = trim(preg_replace("#(\\\\+)|(/+)#", "\\\\", $path), '\\');
            $this->autoGenerateProps->fully_qualified_class = $path;
            return $path;
        }
        return $this->autoGenerateProps->fully_qualified_class;
    }

    public function namespaceToPath($full_path = false)
    {
        return trim(preg_replace("#(\\\\+)|(/+)" . ($full_path === true ? '' : '|(Http\\\\Controllers)') . "#", "/", $this->autoGenerateProps->namespace), '/');
    }

    public function setControllerAndRouteFolderNames()
    {

        $guessed_folder = $this->plainControllerName();
        // Singular was typed
        if ($this->plainControllerName() !== Str::plural($this->plainControllerName())) {
            $guessed_folder = Str::plural($this->plainControllerName()) . '/' . $this->plainControllerName();
        }
        $guessed_folder = $this->autoGenerateProps->controller_subfolder  . '/' . $guessed_folder;

        $this->autoGenerateProps->set('route_folder', strtolower($guessed_folder));
        $this->autoGenerateProps->set('view_folder', strtolower($guessed_folder));

        $subfolder = strtolower($this->ask("Controller subfolder? (eg Web, Api or Admin, / to start from current), A to abort", $guessed_folder));
        if (strtolower($subfolder) == 'a') return false;

        // lets append subfolder to current subfolder if it starts with / and not followed by current subfolder
        if (preg_match("#^/[a-z]+#", $subfolder) && !preg_match("#^/" . $this->autoGenerateProps->controller_subfolder . "+#", $subfolder)) {
            $subfolder = $this->autoGenerateProps->controller_subfolder . $subfolder;
        }


        if (strlen($subfolder) > 1) {
            $parts = explode('/', $subfolder);
            $subfolder = array_reduce($parts, fn ($prev, $curr) => $prev . '/' . Str::studly($curr), '');
        } else $subfolder = ucfirst(strtolower(preg_replace("#Controller$#", "", $this->autoGenerateProps->controller_name)));

        $subfolder = ltrim($subfolder, '/');
        $this->autoGenerateProps->set('controller_subfolder', $subfolder);

        // lets guess default name now if we dont have any yet and subfolder matches at least on (slash) /
        if (!$this->autoGenerateProps->default_name) {
            $parts = explode('/', $subfolder);
            $name = Str::studly(array_slice($parts, -1)[0]);
            $this->autoGenerateProps->set('default_name', $name);
        }
    }

    public function promptControllerName($show_model_prompt = true)
    {
        if ($show_model_prompt === true && $this->autoGenerateProps->recently_created_model) {
            if (!$this->confirm("You recently created a model (" . $this->autoGenerateProps->model_name . "), use the model info?", 'Yes')) $this->promptModel();
        }

        $this->autoGenerateProps->set('controller_subfolder', $this->autoGenerateProps->controller_subfolder_init);

        $controller_name = "";
        while (!preg_match('#[a-z0-9]#i', $controller_name)) {

            // lets prevent pluralizing default_name if it looks like single of another one
            $name = null;
            if ($this->autoGenerateProps->default_name)
                if (preg_match("#" . Str::plural($this->autoGenerateProps->default_name) . '/' . Str::singular($this->autoGenerateProps->default_name) . "#i", $this->autoGenerateProps->controller_subfolder)) {
                    $name = Str::singular($this->autoGenerateProps->default_name) . 'Controller';
                } else if ($this->autoGenerateProps->default_name) {
                    $name = Str::studly($this->autoGenerateProps->default_name);
                    $dontPluralize = ['admin'];
                    $name = (in_array(strtolower($name), $dontPluralize) ? $name : Str::plural($name)) . 'Controller';
                }

            $controller_name = $this->ask("What is the controller name?, A to abort", $name);
            if (strtolower($controller_name) == 'a') return false;
            $controller_name = Str::studly($controller_name);
        }


        // Updating default name
        $this->autoGenerateProps->set('default_name', $controller_name);

        // Let guess the model name
        if (!$this->autoGenerateProps->model_name)
            $this->setModel($controller_name);


        $controller_name = preg_match("#Controller$#", $controller_name) ? $controller_name : $controller_name . 'Controller';
        $this->autoGenerateProps->set('controller_name', $controller_name);
    }

    public function promptControllerFolder()
    {
        // Setting controller's & routes folder
        $proceed = $this->setControllerAndRouteFolderNames();
        if ($proceed === false) return false;

        // set guessed namespace
        $this->setNamespace();
    }
}
