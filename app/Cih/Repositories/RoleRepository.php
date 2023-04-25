<?php

/**
 * Created by PhpStorm.
 * User: iankibet
 * Date: 2016/06/04
 * Time: 7:47 AM
 */

namespace App\Cih\Repositories;

use App\Models\Core\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Str;

class RoleRepository
{
    protected $path;
    protected $user;
    protected $menus;
    protected $allow = false;
    protected $request;
    protected $is_app = 0;
    protected $common;
    protected $userPermissions;
    protected $allPermissionsFile;

    protected $allowedPermissions;
    protected $role;
    protected $urls = [];
    protected $loopLevel = 0;


    public function __construct(Request $request = null)
    {
        if ($request) {
            $this->request = $request;
            $this->user = currentUser();
            $this->path = Route::getFacadeRoot()->current()->uri();
        }
    }

    function getRoutes($routes, $prefix)
    {


        $routes = collect(Route::getRoutes())->filter(function ($route) use ($prefix) {
            return $route->getAction()['prefix'] === $prefix;
        });

        // return $routes;

        $menu = $routes->map(function ($route) use ($prefix) {
            $uri = Str::after($route->uri, $prefix . '/');
            $segements = explode('/', $uri);
            $name = Str::title(str_replace('-', ' ', end($segements)));

            // $uri = route($route->getName() ?? end($segements), ['version' => $segements[0]]);

            $url = $uri;
            return compact('name', 'url');
        })->values()->toArray();

        return $menu;
    }

    public function listRoutes()
    {

        $prefix = 'api/admin';

        $routes = collect(Route::getRoutes())->filter(fn ($route) => Str::startsWith($route->getAction()['prefix'], $prefix))->sort(fn ($a, $b)  => explode('/', $a->getAction()['prefix']) > explode('/', $b->getAction()['prefix']));

        $out = [];

        foreach ($routes as $route) {

            $prefix = $route->getAction()['prefix'];
            $resolve_name = $prefix;

            $parts = explode('/', $prefix);
            $cur = &$out;
            $tmp_path_arr = [];
            foreach ($parts as $part) {

                array_push($tmp_path_arr, $part);

                $tmp_path = implode('/', $tmp_path_arr);

                if ($part !== 'api') {

                    if (!key_exists($part, $cur)) {

                        $cur[$part] = [
                            'children' => [],
                            'path' => $tmp_path !== $prefix ? $tmp_path : trim(preg_replace('#/+#', '/', $resolve_name), '/'),
                            'slug' => $tmp_path !== $prefix ? $tmp_path : trim(preg_replace('#/+#', '.', $resolve_name), '.'),
                            'title' => $tmp_path !== $prefix ? $tmp_path : trim(preg_replace('#/+#', ' > ', $resolve_name), ' > '),
                            'routes' => $tmp_path !== $prefix ? [] : $this->getRoutes($routes, $prefix)
                        ];
                    }

                    $cur = &$cur[$part]['children'];
                }
            }

            unset($cur);
        }

        // dd($out);


        return [
            'code' => 20000,
            'data' => $out
        ];
    }

    public function check()
    {

        return true;
        
        if (currentUser()) {
            $this->authorize();
        } else {
            App::abort(403, "Not authorized to access this page/resource/endpoint");
        }
    }

    protected function authorize()
    {
        $res = $this->getRoutesAndAuthorize();
        if ($res !== true) {
            $this->unauthorized();
        }
    }

    private function getRoutesAndAuthorize()
    {

        $current = $uri = request()->getPathInfo();

        // dd($current);
        $allowed_urls = [];
        $allowed_urls[] = '/';
        $allowed_urls[] = '';
        $allowed_urls[] = 'auth/user';
        $allowed_urls[] = 'auth/password';

        if (in_array($current, $allowed_urls)) {
            return true;
        }

        $parts = explode('/', $current);

        $folder = '';
        $path_to_route_permissions = '';
        $index = '';
        $stack = [];
        $path = '';
        for ($i = count($parts); $i > 0; $i--) {
            $folder = implode('/', $parts);

            if (is_dir(base_path('routes' . $folder))) {
                $index = file_exists(base_path('routes' . $folder . '/' . end($parts) . '.route.php')) ? end($parts) . '.json' : 'index.json';
                $path_to_route_permissions = ltrim($folder . '/' . $index, '/');
                $folder .= '/' . preg_replace('#.json#', '', $index);
                break;
            }

            array_unshift($stack, end($parts));
            array_pop($parts);
        }

        $path = trim(array_reduce($stack, function ($prev, $part) {
            if (!is_numeric($part)) {
                $prev .= '.' . $part;
            }
            return $prev;
        }, ''), '.');

        $file = 'app/permissions/' . $path_to_route_permissions;

        if (!File::exists(storage_path($file))) {
            File::ensureDirectoryExists(storage_path($folder));
            File::put(storage_path($file), 'null');
            File::chmod(storage_path($file), 774);
        }

        $routes = json_decode(File::get(storage_path($file)));

        if (is_array($routes) && count($routes) > 0) {

            $slug = false;
            foreach ($routes as $route) {
                if ($route->path == $path) {
                    $slug = trim(preg_replace('#/#', '.', $folder) . '.' . $route->slug, '.');
                    break;
                }
            }

            if ($slug !== null) {

                $res = compact('folder', 'slug');
                return $this->isAllowed($res);
            }

            return 2;
        } else return 1;
    }


    protected function isAllowed($routes)
    {

        $module = PermissionGroup::where('id', $this->user->permission_group_id)
            ->first();

        $permissions = [];
        if (isset($module->permissions))
            $permissions = json_decode($module->permissions);

        $r = $routes['slug'];
        $subject = trim(preg_replace('#api.admin.#', '', $r), '.');
        $subject = $this->cleanArray([$subject], '.')[0];

        if (in_array($subject, $permissions)) return true;
        else return false;
    }

    function cleanArray($array, $separator)
    {
        return
            array_values(array_unique(
                array_map(function ($item) use ($separator) {
                    $parts = explode($separator, $item);

                    $arr = [];
                    $prevPart = null;
                    foreach ($parts as $part) {
                        if ($prevPart != $part) {
                            $arr[] = $part;
                        }

                        $prevPart = $part;
                    }

                    return implode($separator, $arr);
                }, $array)
            ));
    }


    public function unauthorized()
    {
        $common_paths = ['logout', 'login', 'register'];
        $path = $this->path;
        if (!in_array($path, $common_paths)) {
            App::abort(403, "Not authorized to access this page/resource/endpoint");
            die('You are not authorized to perform this action');
        }
    }
}
