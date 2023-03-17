<?php

namespace App\Cih\Commands\Autogenerate\Traits;

use Illuminate\Support\Str;

trait CreateRouteOrViewTrait
{

    public function createRouteOrView($create_view = false)
    {

        // prompt route or view name
        $res = $this->promptRouteOrViewName($create_view);
        if ($res === false) return false;


        // this method will set for us the route_or_view_folder as per the user input else defaults will be used
        $route_or_view_folder = $this->promptRouteOrViewSubFolder($create_view);
        if ($route_or_view_folder === false) return false;

        $route_parts = explode('/', $route_or_view_folder);

        // incase if routes_folder was changed by user
        $route_slug = trim(implode('/', $route_parts), '/');

        $this->autoGenerateProps->set('route_folder', $route_slug);

        // guessed namespace
        $fully_qualified_class = array_reduce(explode('/', $route_slug), fn ($prev, $curr) => $prev . '\\' . Str::studly($curr), '') . '\\' . $this->autoGenerateProps->controller_name;
        $this->setControllerFullyQualifiedClassName($fully_qualified_class);


        // Default route name
        $default_name = strtolower($this->autoGenerateProps->default_name);

        $nam = Str::slug(array_slice($route_parts, -1)[0], '');
        if (!$default_name) {
            $default_name = $nam;
        }

        // Singular was typed
        if ($this->plainControllerName() !== Str::plural($default_name)) {
            $default_name = $nam;
        }

        $this->autoGenerateProps->set('is_singular', false);
        if ($default_name !== Str::plural($default_name))
            $this->autoGenerateProps->set('is_singular', true);


        if ($create_view === true)
            $name = $this->autoGenerateProps->view_index === 'index' ? 'index' : strtolower($this->autoGenerateProps->default_name);
        else
            $name = $this->autoGenerateProps->route_index === 'index' ? 'index' : strtolower($this->autoGenerateProps->default_name);

        $this->autoGenerateProps->set('route_or_view_name', $name);

        // final path
        $path = trim($route_or_view_folder . '/' . $name, '/');


        $confirmed = false;
        while ($confirmed === false) {

            if ($create_view === true)
                $confirmed = $this->confirm("The view path will be: views/" . $path . ".blade.php \nIs everything alright?");
            else
                $confirmed = $this->confirm("The route path will be: routes/" . $path . ".route.php \nIs everything alright?");

            if ($confirmed === false) {
                // Revert updated properties
                $this->rollbackProps();
                $this->createRouteOrView($create_view);
            }
        }

        return strtolower($path);
    }


    public function promptRouteOrViewName($create_view)
    {

        // Default route name
        $default_name = strtolower(Str::slug(preg_replace("#Controller$#", "", $this->autoGenerateProps->controller_name)));

        $route_name = '';
        while (!preg_match('#[a-z0-9]#i', $route_name)) {
            $route_name = strtolower($this->ask(($create_view === true ? "View name (without .blade.php)" : "Route name (without .route.php)") . ', A to abort', $default_name));
            if (strtolower($route_name) == 'a') return false;
        }

        // Updating default name
        $this->autoGenerateProps->set('default_name', $route_name);

        // guessed controller name
        $this->autoGenerateProps->set('controller_name', Str::studly($route_name) . 'Controller');

        // guessed model name
        if (!$this->autoGenerateProps->model_name)
            $this->setModel($route_name);
    }

    public function promptRouteOrViewSubFolder($create_view)
    {
        $msg = ($create_view === true ? 'View' : 'Route') . " subfolder? (eg web, api or admin, / to start from current) A to abort";

        $guessed_folder = $this->autoGenerateProps->default_name;
        // Singular was typed
        if ($this->autoGenerateProps->default_name !== Str::plural($this->autoGenerateProps->default_name)) {
            $guessed_folder = Str::plural($this->autoGenerateProps->default_name) . '/' . $this->autoGenerateProps->default_name;
        }

        if ($create_view === true)
            $subfolder = $this->autoGenerateProps->view_folder;
        else
            $subfolder = $this->autoGenerateProps->route_folder;

        if (!preg_match("#" . $guessed_folder . "$#", $subfolder))
            $guessed_folder = strtolower($subfolder  . '/' . strtolower($guessed_folder));
        else
            $guessed_folder = $subfolder;


        if ($create_view === true) {
            $subfolder = $this->ask($msg, $guessed_folder);
            if (strlen($subfolder) > 0) $this->autoGenerateProps->set('view_folder', Str::slug($subfolder, "/"));
        } else {
            $subfolder = $this->ask($msg, $guessed_folder);
        }

        if ($create_view === true) {
            // lets append subfolder to current subfolder if it starts with / and not followed by current subfolder
            if (preg_match("#^/[a-z]+#", $subfolder) && !preg_match("#^/" . $this->autoGenerateProps->view_folder . "+#", $subfolder)) {
                $subfolder = $this->autoGenerateProps->view_folder . $subfolder;
            }
        } else {
            // lets append subfolder to current subfolder if it starts with / and not followed by current subfolder
            if (preg_match("#^/[a-z]+#", $subfolder) && !preg_match("#^/" . $this->autoGenerateProps->route_folder . "+#", $subfolder)) {
                $subfolder = $this->autoGenerateProps->route_folder . $subfolder;
            }
        }


        if (strtolower($subfolder) == 'a') return false;

        $subfolder = trim(preg_replace("#/+#", "/", $subfolder), '/');

        return $subfolder;
    }

    function setPath($path)
    {
        $this->autoGenerateProps->set('path', $path);
        $this->autoGenerateProps->set('controller_subfolder', $path);
    }

    public function setControllerFullyQualifiedClassName($fully_qualified_class = null)
    {
        if (!$fully_qualified_class)
            $fully_qualified_class = $this->autoGenerateProps->controller_folder . '/' . $this->autoGenerateProps->controller_subfolder . '/' . $this->autoGenerateProps->controller_name;

        $parts = explode('/', $fully_qualified_class);
        $this->autoGenerateProps->set('fully_qualified_class', 'App\Http\Controllers\\' . ltrim(preg_replace("#\\\\+#", "\\\\", array_reduce($parts, fn ($prev, $curr) => $prev . '\\' . Str::studly($curr), '')), '\\'));
    }
}
