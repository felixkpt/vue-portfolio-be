<?php

namespace App\Http\Controllers\Admin\Settings\Permissiongroups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use App\Models\Core\PermissionGroup;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class PermissionGroupsController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return permissiongroup's index view
     */
    public function index()
    {
        return view($this->folder . 'permission_groups', []);
    }

    /**
     * store permissiongroup
     */
    public function storePermissionGroup()
    {

        request()->validate($this->getValidationFields(PermissionGroup::class, ['user_id', 'slug', 'is_default', 'permissions']));

        $data = \request()->all();

        $data['slug'] = Str::slug($data['slug']);

        $permissions = $data['permissions'];
        $routes = $data['routes'];

        $permissions = $this->cleanArray($permissions, '.');
        $routes = $this->cleanArray($routes, '/');

        $data['permissions'] = json_encode($permissions);
        $data['routes'] = json_encode($routes);

        // dd($data['permissions']);

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('permission_groups', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }
        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
        }

        $data['is_default'] = false;
        if (PermissionGroup::count() === 0)
            $data['is_default'] = true;

        $res = PermissionGroup::updateOrCreate(['id' => request()->id], $data);
        return response(['type' => 'success', 'message' => 'PermissionGroup ' . $action . ' successfully', 'data' => $res]);
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
    public function listPermissionGroups()
    {
        $permissiongroups = PermissionGroup::all();

        return [
            'code' => 20000,
            'data' => $permissiongroups
        ];


        // old code
        // $permissiongroups = PermissionGroup::where([]);

        // if (\request('all')) {
        //     if (Schema::hasColumn('permission_groups', 'status')) return $permissiongroups->where('status', 1)->get();
        //     else return $permissiongroups->get();
        // }

        // return SearchRepo::of($permissiongroups)
        //     ->addColumn('action', function ($permissiongroup) {
        //         $str = '';
        //         $json = json_encode($permissiongroup);
        //         $str .= '<a href="javascript:void" data-model="' . htmlentities($json, ENT_QUOTES, 'UTF-8') . '" onclick="prepareEdit(this,\'permissiongroup_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
        //         //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/permissiongroups/delete').'\',\''.$permissiongroup->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
        //         return $str;
        //     })->make();
    }

    function getRoutes($routes, $resolve_name, $prefix)
    {


        $routes = collect(Route::getRoutes())->filter(function ($route) use ($prefix) {
            return $route->getAction()['prefix'] === $prefix;
        });

        // return $routes;

        $menu = $routes->map(function ($route) use ($resolve_name, $prefix) {
            $uri = Str::after($route->uri, $prefix . '/');
            $segements = explode('/', $uri);
            $name = Str::title(str_replace('-', ' ', end($segements)));

            // $uri = route($route->getName() ?? end($segements), ['version' => $segements[0]]);

            $resolve_name .= '/' . $uri;
            return [
                'children' => [],
                'path' => trim(preg_replace('#/+#', '/', $resolve_name), '/'),
                'slug' => trim(preg_replace('#/+#', '.', $resolve_name), '.'),
                'title' => Str::title(trim(preg_replace('#/+#', ' ', $uri), ' ')),
                'routes' => []
            ];
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
                            'routes' => $tmp_path !== $prefix ? [] : $this->getRoutes($routes, $resolve_name, $prefix)
                        ];
                    }

                    $cur = &$cur[$part]['children'];
                }
            }

            unset($cur);
        }

        // dd($out['admin']);

        // unset($out['admin']['children']);

        return [
            'code' => 20000,
            'data' => $out
        ];
    }
    function dirsToTree($dirs)
    {
        $tree = [];

        $prevNode = null;
        foreach ($dirs as $dir) {

            $currNode = trim(preg_replace('#/#', '.', preg_replace('#permissions/api#', '', $dir)), '.');
            if ($prevNode === null) {
                $prevNode = $currNode;
            } else if (!preg_match("#" . $prevNode . "#", $currNode)) {
                $prevNode = $currNode;
            }

            if (strlen($currNode) < 1) continue;

            dd($currNode);
        }

        return $dirs;
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
    public function destroyPermissionGroup($permissiongroup_id)
    {
        $permissiongroup = PermissionGroup::findOrFail($permissiongroup_id);
        if ($permissiongroup->is_default)
            return response(['type' => 'failure', 'message' => 'Default PermissionGroup cannot be deleted']);

        $permissiongroup->delete();
        return response(['type' => 'success', 'message' => 'PermissionGroup deleted successfully']);
    }
}
