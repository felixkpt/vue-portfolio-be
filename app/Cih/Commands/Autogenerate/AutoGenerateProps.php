<?php

namespace App\Cih\Commands\Autogenerate;

use Exception;
use ReflectionClass;

class AutoGenerateProps
{
    public $model_name;
    public $model_folder = 'Core';
    public $model_namespace;
    public $fields;
    public $plain_fields;
    public $model_fields;
    public $view_title;

    // Route subfolder and view subfolder should be the same so as to make the process of mapping Route > Controller > View easier
    public $default_name;
    public $recently_created_migration = false;
    public $recently_created_model = false;
    public $path = '';
    public $mutations = [];
    public $route_folder = 'admin';
    public $route_folder_init = 'Admin';
    public $view_folder = 'admin';
    public $view_folder_init = 'Admin';

    public $controller_name;
    public $controller_folder = 'Http/Controllers';
    public $controller_subfolder = 'Admin';
    public $controller_subfolder_init = 'Admin';
    public $fully_qualified_class = 'full_controller_path';
    public $namespace = 'the_namespace';
    public $real_controller;
    public $is_singular = false;
    /**
     * @prop route_index either index|filename
     */
    public $route_index = 'filename';
    /**
     * @prop route_index either index|filename
     */
    public $view_index = 'index';
    
    public $route_or_view_name;

    public function set($prop, $val)
    {
        $this->mutations[$prop] = $this->{$prop};
        $this->{$prop} = $val;
    }

    public function save($props)
    {
        session()->put('autoGenerateProps', $props);
    }
}
