<?php

namespace App\Http\Controllers\Api\Admin\PermissionGroups;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Cih\Repositories\ShRepository;

use Illuminate\Http\Request;

use App\Models\Core\Module;
use App\Cih\Repositories\SearchRepo;
use App\Models\Core\PermissionGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PermissionsController extends Controller
{

    public function __construct()
    {
        $this->api_model = Module::class;
    }

    public function storePermissionGroup($id = 0)
    {
        $data = \request()->all();
        $valid = Validator::make($data, ShRepository::getValidationRules($this->api_model));
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }

        $permissiongroup = ShRepository::autoSaveModel($this->api_model, $data);
        if ($id) {
            $data['id'] = $id;
        }
        if (isset($data['id']) && $data['id'] > 0) {
            ShRepository::storeLog('updated_PermissionGroup', "Updated PermissionGroup # $permissiongroup->id $permissiongroup->name", $permissiongroup);
        } else {
            ShRepository::storeLog("created_PermissionGroup", "Created new PermissionGroup #$permissiongroup->id $permissiongroup->name", $permissiongroup);
        }
        return [
            'status' => 'success',
            'permissiongroup' => $permissiongroup
        ];
    }

    public function listPermissionGroups()
    {
        $user = \request()->user();
        $permissiongroups = new PermissionGroup();
        $table = 'PermissionGroups';
        $search_keys = ShRepository::getFillables($permissiongroups);
        return [
            'status' => 'success',
            'data' => SearchRepo::of($permissiongroups, $table, $search_keys)
                ->make(true)
        ];
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

    public function updatePermissions($id)
    {
        $permissiongroup = PermissionGroup::find($id);
        $permissiongroup->permissions = request('permissions');
        $permissiongroup->save();
        ShRepository::storeLog("update_PermissionGroup_permissions", "Updated PermissionGroup permissions for PermissionGroup#$permissiongroup->id $permissiongroup->name", $permissiongroup);
        return [
            'status' => 'success',
            'permissiongroup' => $permissiongroup
        ];
    }

    public function getPermissionGroup($id)
    {
        $user = \request()->user();
        $permissiongroup = PermissionGroup::find($id);
        return [
            'status' => 'success',
            'permissiongroup' => $permissiongroup
        ];
    }

    public function deletePermissionGroup($id)
    {
        $user = \request()->user();
        //        $permissiongroup = PermissionGroup::find($id);
        $permissiongroup = PermissionGroup::where('user_id', $user->id)->find($id);
        $permissiongroup->delete();
        return [
            'status' => 'success',
        ];
    }

    public function allPermissions()
    {
        $adminPermissions = RoleRepository::getRolePermissions('admin');
        return $adminPermissions;
    }
}
