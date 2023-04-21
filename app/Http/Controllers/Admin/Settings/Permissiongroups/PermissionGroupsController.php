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

        $permissions = [];
        $routes = [];
        foreach ($data['routes'] as $route) {
            $permissions[] = $route['slug'];
            $routes[] = $route['route'];
        }

        $data['slug'] = Str::slug($data['slug']);
        $data['permissions'] = json_encode($permissions);
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

        $data['is_default'] = false;
        if (PermissionGroup::count() === 0)
            $data['is_default'] = true;

        $res = PermissionGroup::updateOrCreate(['id' => request()->id], $data);
        return response(['type' => 'success', 'message' => 'PermissionGroup ' . $action . ' successfully', 'data' => $res]);
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
        $permissiongroups = PermissionGroup::where([]);

        if (\request('all')) {
            if (Schema::hasColumn('permission_groups', 'status')) return $permissiongroups->where('status', 1)->get();
            else return $permissiongroups->get();
        }

        return SearchRepo::of($permissiongroups)
            ->addColumn('action', function ($permissiongroup) {
                $str = '';
                $json = json_encode($permissiongroup);
                $str .= '<a href="javascript:void" data-model="' . htmlentities($json, ENT_QUOTES, 'UTF-8') . '" onclick="prepareEdit(this,\'permissiongroup_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
                //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/permissiongroups/delete').'\',\''.$permissiongroup->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    public function listRoutes()
    {

        $json = json_decode(Storage::get('permissions/routes.json'));
     
        return [
            'code' => 20000,
            'data' => $json
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
    public function destroyPermissionGroup($permissiongroup_id)
    {
        $permissiongroup = PermissionGroup::findOrFail($permissiongroup_id);
        if ($permissiongroup->is_default)
            return response(['type' => 'failure', 'message' => 'Default PermissionGroup cannot be deleted']);

        $permissiongroup->delete();
        return response(['type' => 'success', 'message' => 'PermissionGroup deleted successfully']);
    }
}
