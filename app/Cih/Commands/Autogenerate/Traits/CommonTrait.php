<?php

namespace App\Cih\Commands\Autogenerate\Traits;

use Illuminate\Support\Str;

trait CommonTrait
{

    /**
     * autoGenerateProps holds properties that can be shared during autogeneration of Migration, Model, Contoller, Route or View
     */
    protected $autoGenerateProps;

    public function getRouteUrlFromControllerNamespace()
    {
        if ($this->autoGenerateProps->fully_qualified_class) {
            $str = trim(preg_replace("#(\\\\+)|(/+)|(Controller$)#", "/", $this->autoGenerateProps->fully_qualified_class), '/');
            return strtolower(trim(preg_replace("#(app/http/controllers)#", "", $str), '/'));
        }

        return $this->autoGenerateProps->route_folder;
    }

    public function storeFile($original_path, $contents)
    {
        $original_path = str_replace('\\', '/', $original_path);
        $path_arr = explode("/", $original_path);
        unset($path_arr[count($path_arr) - 1]);
        $dir = implode("/", $path_arr);
        exec("mkdir -p $dir");
        file_put_contents($original_path, $contents);
    }

    public function replace_first($find, $replace, $subject)
    {
        return implode($replace, explode($find, $subject, 2));
    }

    public function replaceVars($content, $t = false)
    {
        $model = strtolower($this->autoGenerateProps->model_name);
        $umodel = $this->autoGenerateProps->model_name;
        $models = null;
        $umodels = null;

        if ($model)
            $models = Str::plural($model);
        if ($umodel)
            $umodels = Str::plural($umodel);

        $route_index = $this->autoGenerateProps->route_index === 'index' ? 'index' : strtolower($this->autoGenerateProps->default_name);
        $view_index = $this->autoGenerateProps->view_index === 'index' ? 'index' : strtolower($this->autoGenerateProps->default_name);

        $new_content = str_replace('{model}', $model, $content);
        $new_content = str_replace('{cmodel}', strtoupper($model), $new_content);
        $new_content = str_replace('{title}', $this->autoGenerateProps->view_title, $new_content);
        $new_content = str_replace('{models}', $models, $new_content);
        $new_content = str_replace('{umodel}', $umodel, $new_content);
        $new_content = str_replace('{route_name}', $route_index, $new_content);
        $new_content = str_replace('{view_name}', $view_index, $new_content);
        $new_content = str_replace('{umodels}', $umodels, $new_content);
        $new_content = str_replace('{smumodel}', strtolower($umodel), $new_content);
        $new_content = str_replace('{route_folder}', $this->autoGenerateProps->route_folder, $new_content);
        $new_content = str_replace('{full_controller_path}', $this->autoGenerateProps->fully_qualified_class, $new_content);
        $new_content = str_replace('{controller}', $this->autoGenerateProps->controller_name, $new_content);
        $new_content = str_replace('{model_fields}', ('"id", ' . ($this->autoGenerateProps->model_fields ? $this->autoGenerateProps->model_fields . ', ' : '') . '"action"'), $new_content);
        $new_content = str_replace('{full_model_path}', str_replace('/', '\\', $this->autoGenerateProps->model_namespace), $new_content);
        $new_content = str_replace('{route_url}', strtolower($this->autoGenerateProps->path), $new_content);
        return $new_content;
    }

    function setModel($model_name)
    {
        $this->autoGenerateProps->set('default_name', $model_name);

        $model_name = Str::singular(Str::studly($model_name));

        $this->autoGenerateProps->set('model_name', $model_name);

        $model_namespace = trim($this->autoGenerateProps->model_folder . "/" . $model_name, '/');
        $this->autoGenerateProps->set('model_namespace', 'App\\Models\\' . $model_namespace);

    }

    public function rollbackProps()
    {
        foreach ($this->autoGenerateProps->mutations as $key => $mutation)
            $this->autoGenerateProps->{$key} = $mutation;
    }

    public function alertUser($message)
    {
        return $this->alert('####### ' . $message . ' #######');
    }

    public function plainControllerName()
    {
        return preg_replace("#Controller$#", "", $this->autoGenerateProps->controller_name);
    }
}
