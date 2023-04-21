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

    function getValidationFields(string $model, array $except = [])
    {
        $model = new $model();
        $fillables = $model->getFillable();

        $fillables = array_values(array_diff($fillables, $except));

        $validation_array = [];
        foreach ($fillables as $field) {
            $validation_array[$field] = 'required';
        }
        if (in_array("file", $fillables)) {
            $validation_array['file'] = 'required|max:50000';
        }

        return $validation_array;
    }

}
