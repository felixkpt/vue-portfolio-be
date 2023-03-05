<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;


$middleWares = env('SH_API_MIDDLEWARE', 'auth:sanctum');
$middleWares = explode(',', $middleWares);
$middleWares = ['sh_auth'];

$prefix = '';
Route::group(['middleware' => $middleWares, 'prefix' => $prefix], function () use ($prefix) {

    $routes_path = base_path('routes' . $prefix);
    if (file_exists($routes_path)) {
        $route_files = File::allFiles(base_path('routes/' . $prefix));

        foreach ($route_files as $file) {
            $path = $file->getPath();
            $file_name = $file->getFileName();
            $prefix = str_replace($file_name, '', $path);
            $prefix = str_replace($routes_path, '', $prefix);
            $file_path = $file->getPathName();
            $this->route_path = $file_path;
            $arr = explode('/', $prefix);
            $len = count($arr);
            $main_file = $arr[$len - 1];
            $arr = array_map('ucwords', $arr);
            $arr = array_filter($arr);
            $ext_route = str_replace('user.route.php', '', $file_name);
            if ($main_file . '.route.php' === $ext_route)
                $ext_route = str_replace($main_file . '.', '.', $ext_route);
            $ext_route = str_replace('.route.php', '', $ext_route);
            //            $ext_route = str_replace('web', '', $ext_route);
            if ($ext_route)
                $ext_route = '/' . $ext_route;
            $prefix = strtolower($prefix . $ext_route);
            $namespace = implode('\\', $arr);
            $namespace = str_replace('\\\\', '\\', $namespace);
            Route::group(['namespace' => $namespace, 'prefix' => $prefix], function () {
                require $this->route_path;
            });
        }
    }
});
