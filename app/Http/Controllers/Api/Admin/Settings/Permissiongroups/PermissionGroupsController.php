<?php

namespace App\Http\Controllers\Api\Admin\Settings\Permissiongroups;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use App\Models\Core\PermissionGroup;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class PermissionGroupsController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;
    protected $leftTrim = 'api';

    /**
     * return permissiongroup's index view
     */
    public function index()
    {
        $permissiongroups = PermissionGroup::all();

        return [
            'code' => 200,
            'data' => $permissiongroups
        ];
    }

    /**
     * store permissiongroup
     */
    public function store()
    {

        request()->validate($this->getValidationFields(PermissionGroup::class, ['user_id', 'slug', 'is_default', 'slugs', 'methods', 'routes']));

        $data = \request()->all();

        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);

        $routes = (array) $data['routes'];

        $arr = [];
        foreach ($routes as $route) {


            $key = $this->search($route['path'], $arr, 'path');
            if ($key === null)
                $arr[] = $route;
            else
                $arr[$key] = [...$arr[$key], 'methods' => array_values(array_unique([...$arr[$key]['methods'], ...$route['methods']]))];
        }

        [$routes, $slugs] = $this->routesSeparete($arr);

        $data['slugs'] = json_encode($slugs);
        $data['routes'] = json_encode($routes);

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('permission_groups', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }
        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
        }

        if (!request()->id) {
            $data['is_default'] = false;
            if (PermissionGroup::count() === 0)
                $data['is_default'] = true;
        }

        $res = PermissionGroup::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);
        return response(['type' => 'success', 'message' => 'PermissionGroup ' . $action . ' successfully', 'data' => $res]);
    }

    function search($id, $array, $search_key)
    {
        foreach ($array as $key => $val) {
            if ($val[$search_key] === $id) {
                return $key;
            }
        }
        return null;
    }

    function routesSeparete($arr)
    {

        $routes = $slugs = [];
        foreach ($arr as $route) {
            [$routes[], $slugs[]] = [$route['path'], $route['slug']];
        }

        return [$routes, $slugs];
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

    /**
     * return permissiongroup values
     */
    public function list()
    {
        $permissiongroups = PermissionGroup::all();

        return [
            'code' => 200,
            'data' => $permissiongroups
        ];
    }

    function getRoutes($routes, $resolve_name, $prefix)
    {

        $routes = collect(Route::getRoutes())->filter(function ($route) use ($prefix) {
            return $route->getAction()['prefix'] === $prefix;
        });

        // return $routes;

        $menu = $routes->map(function ($route) use ($resolve_name, $prefix) {

            $uri = trim(preg_replace('#^' . $prefix . '#', '', $route->uri), '/');

            $name = str_replace('-', ' ', $uri);
            if ($route->uri == $route->getAction()['prefix'])
                $name = 'index';

            if (isset($route->action['controller']))
                [$namespace, $name] = explode('@', $route->action['controller']);

            if ($route->getName()) {
                $parts = explode('.', $route->getName());
                $name = end($parts);
            }

            $resolve_name .= '/' . trim(preg_replace('#^' . $this->leftTrim . '#', '', $uri), '/');
            $resolve_name = trim(preg_replace('#/+#', '/', $resolve_name), '/');

            return [
                'children' => [],
                'path' => $this->resolve($resolve_name, '/') . '@' . implode('|@', $route->methods()),
                'slug' => $this->resolve($resolve_name, '.'),
                'title' => Str::title(trim(preg_replace('#/+|_|-#', ' ', $name), ' ')),
                'routes' => []
            ];
        })->values()->toArray();

        return $menu;
    }

    function resolve($resolve_name, $seperator)
    {
        return trim(preg_replace('#/+#', $seperator, $resolve_name), $seperator);
    }
    public function listRoutes()
    {

        $prefix = 'api/admin';

        $routes = collect(Route::getRoutes())->filter(fn ($route) => Str::startsWith($route->getAction()['prefix'], $prefix))->sort(fn ($a, $b)  => explode('/', $a->getAction()['prefix']) > explode('/', $b->getAction()['prefix']));

        $out = [];

        foreach ($routes as $route) {

            $prefix = $route->getAction()['prefix'];

            $resolve_name = $prefix;
            $resolve_name = preg_replace('#^' . $this->leftTrim . '#', '', $resolve_name);

            $parts = explode('/', $prefix);
            $cur = &$out;
            $tmp_path_arr = [];
            foreach ($parts as $part) {

                array_push($tmp_path_arr, $part);

                $tmp_path = implode('/', $tmp_path_arr);

                $tmp_path_echo = trim(preg_replace('#^' . $this->leftTrim . '#', '', $tmp_path), '/');

                $tmp_path == $tmp_path_echo;

                if ($part !== 'api') {

                    if (!key_exists($part, $cur)) {

                        $nam = explode('/', $resolve_name);
                        $nam = end($nam);

                        $path = $tmp_path !== $prefix ? $tmp_path_echo : trim(preg_replace('#/+#', '/', $resolve_name), '/');
                        $cur[$part] = [
                            'children' => [],
                            'path' => $path,
                            'slug' => $tmp_path !== $prefix ? $tmp_path_echo : trim(preg_replace('#/+#', '.', $resolve_name), '.'),
                            'title' => Str::title($tmp_path !== $prefix ? $tmp_path_echo : $nam),
                            'routes' => $tmp_path !== $prefix ? [] : $this->getRoutes($routes, $resolve_name, $prefix)
                        ];
                    }

                    $cur = &$cur[$part]['children'];
                }
            }

            unset($cur);
        }

        // dd($out);

        // unset($out['admin']['children']);

        return [
            'code' => 20000,
            'data' => $out
        ];
    }

    public function listRoles()
    {

        $permissiongroups = PermissionGroup::all();

        return [
            'code' => 20000,
            'data' => $permissiongroups
        ];
    }

    public function addUser()
    {
        dd('is to add');
        $permissiongroup = PermissionGroup::findOrFail(\request('permission_group_id'));
        $user = User::findOrFail(\request('user_id'));
        $user->permission_group_id = $permissiongroup->id;
        $user->save();

        $this->user_mapping_update($user, true);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'User added successfully']);
    }

    public function removeUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return back()->with('notice', ['type' => 'warning', 'message' => 'You cannot remove yourself from a Permission Group']);
        } else {
            $this->user_mapping_update($user);
            $user->permission_group_id = 0;
            $user->save();
            return back()->with('notice', ['type' => 'success', 'message' => 'User removed successfully']);
        }
    }

    /**
     * toggle permissiongroup status
     */
    public function togglePermissionGroupStatus($permissiongroup_id)
    {
        $permissiongroup = PermissionGroup::findOrFail($permissiongroup_id);
        $state = $permissiongroup->status == 1 ? 'Deactivated' : 'Activated';
        $permissiongroup->status = $permissiongroup->status == 1 ? 0 : 1;
        $permissiongroup->save();
        return response(['type' => 'success', 'message' => 'PermissionGroup #' . $permissiongroup->id . ' has been ' . $state]);
    }

    /**
     * delete permissiongroup
     */
    public function destroy($permissiongroup_id)
    {
        $permissiongroup = PermissionGroup::findOrFail($permissiongroup_id);
        if ($permissiongroup->is_default)
            return response(['type' => 'failure', 'message' => 'Default PermissionGroup cannot be deleted']);

        $permissiongroup->delete();
        return response(['type' => 'success', 'message' => 'PermissionGroup deleted successfully']);
    }
}
