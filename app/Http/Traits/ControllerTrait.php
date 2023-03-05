<?php

namespace App\Http\Traits;

trait ControllerTrait
{

    public $folder = '';

    public function __construct()
    {
        $class = get_class($this);

        $class = str_replace('App\\Http\\Controllers\\', "", $class);

        $arr = explode('\\', $class);
        unset($arr[count($arr) - 1]);
        $folder = implode('.', $arr) . '.';
        $this->folder = strtolower($folder);
    }
}
