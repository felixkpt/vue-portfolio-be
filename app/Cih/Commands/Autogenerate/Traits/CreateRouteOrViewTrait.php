<?php

namespace App\Cih\Commands\Autogenerate\Traits;

use Illuminate\Support\Str;

trait CreateRouteOrViewTrait
{

    public function createRouteOrView($create_view = false)
    {

        // this method will set for us the route_or_view_folder as per the user input else defaults will be used
        $route_or_view_folder = $this->getRouteOrViewSubFolder($create_view);
        if ($route_or_view_folder === false) return false;
        $route_parts = explode('/', $route_or_view_folder);

        // Default route name
        $default_route_name = strtolower(Str::singular(preg_replace("#Controller$#", "", $this->autoGenerateProps->controller_name)));
        if (!$default_route_name) {
            $default_route_name = Str::slug(array_slice($route_parts, -1)[0], '');
        }

        // if ($default_route_name === $route_or_view_folder)
        //     $default_route_name = '';
        $route_name = '';
        while (!preg_match('#[a-z0-9]#i', $route_name)) {
            $route_name = strtolower($this->ask(($create_view === true ? "View name (without .blade.php)" : "Route name (without .route.php)") . ', A to abort', $default_route_name));
            if (strtolower($route_name) == 'a') return false;
        }

        // Updating default name
        $this->autoGenerateProps->set('default_name', $route_name);

        // guessed controller name
        if (!$this->autoGenerateProps->controller_name) {
            $this->autoGenerateProps->set('controller_name', Str::studly($route_name) . 'Controller');
        }
        // guessed model name
        if (!$this->autoGenerateProps->model_name) {
            $this->setModel(Str::studly(Str::singular($route_name)));
        }

        // incase if routes_folder was changed by user
        $route_slug = trim(implode('/', $route_parts), '/');

        $this->autoGenerateProps->set('route_folder', $route_slug);

        // guessed namespace
        $fully_qualified_class = array_reduce(explode('/', $route_slug), fn ($prev, $curr) => $prev . '\\' . Str::studly($curr), '') . '\\' . $this->autoGenerateProps->controller_name;

        $this->setControllerFullyQualifiedClassName($fully_qualified_class);

        // final path
        $path = trim($route_or_view_folder . '/' . $route_name, '/');

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

        return $path;
    }

    public function getRouteOrViewSubFolder($create_view)
    {
        $msg = "What is the " . ($create_view === true ? 'view' : 'route') . " subfolder? eg web, api or admin, A to abort";

        $folder = strtolower($this->autoGenerateProps->path);

        if ($create_view === true)
            $folder = $this->autoGenerateProps->view_folder;
        else
            $folder = $this->autoGenerateProps->route_folder;

        if ($create_view === true) {
            $subfolder = $this->ask($msg, $folder);
            if (strlen($subfolder) > 0) $this->autoGenerateProps->set('view_folder', Str::slug($subfolder, "/"));
        } else {
            // $p = preg_replace("#(app/http/controllers)#", "", $this->autoGenerateProps->controller_subfolder);
            $subfolder = $this->ask($msg, $folder);
            // $subfolder  = strtolower($this->autoGenerateProps->ask($msg, strtolower($p ?: $this->autoGenerateProps->route_folder)));
        }

        if (strtolower($subfolder) == 'a') return false;

        return trim(preg_replace("#/+#", "/", $subfolder), '/');
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
