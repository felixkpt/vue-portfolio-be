<?php

namespace App\Http\Controllers\Api\Admin\PermissionGroups\PermissionGroup;

use App\Http\Controllers\Controller;
use App\Models\Core\PermissionGroup;
use App\Models\Core\Permission;
use App\Repositories\RoleRepository;
use App\Repositories\SearchRepo;
use Couchbase\Role;
use App\Repositories\ShRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function listModules($permissiongroup_id)
    {
        $modules = Permission::where('permission_group_id', $permissiongroup_id);
        return [
            'status' => 'success',
            'data' => SearchRepo::of($modules)
                ->make(true)
        ];
    }
    public function listAllModules($role, $id)
    {
        $permissiongroup = PermissionGroup::find($id);
        $modules = RoleRepository::getRolePermissions($role, true);
        $commonIndex = array_search('common', $modules, true);
        if ($commonIndex !== false) {
            unset($modules[$commonIndex]);
        }
        $modules = array_values($modules);
        $modules = array_unique($modules);
        return [
            'modules' => $modules,
            'permissiongroup' => $permissiongroup,
            'PermissionGroupModules' => $permissiongroup->permissions()->pluck('module')->toArray()
        ];
    }
    public function listPendingModules($permissiongroup_id)
    {
        $modules = Permission::where('permission_group_id', $permissiongroup_id)->pluck('module')->toArray();
        $adminPermissions = RoleRepository::getRolePermissions('admin', true);
        $new = [];
        foreach ($adminPermissions as $module) {
            if (in_array($module, $modules))
                continue;
            $new[] = [
                'id' => $module,
                'name' => ucwords(str_replace('_', ' ', $module))
            ];
        }
        return [
            'status' => 'success',
            'data' => $new
        ];
    }
    public function addModule($permissiongroup_id)
    {
        $data = \request()->all();

        $rules = [
            'permission_module' => 'required'
        ];
        $valid = Validator::make($data, $rules);
        if ($valid->errors()->count()) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }
        $module = Permission::updateOrCreate([
            'permission_group_id' => $permissiongroup_id,
            'module' => $data['permission_module']
        ], [
            'permission_group_id' => $permissiongroup_id,
            'module' => $data['permission_module']
        ]);
        $module = Permission::find($module->id);
        if (!$module->permissions) {
            $module->permissions = [$module->module];
        }
        $roleRepo = new RoleRepository();
        $allowed_urls = $roleRepo->extractRoleUrls($module->module, $module->permissions, 'admin');
        $module->urls = $allowed_urls;
        session()->put('permissions', null);
        session()->put('allowed_urls', null);
        return response([
            'status' => 'success',
            'module' => $module
        ]);
    }

    public function getModule($module_id)
    {
        $module = Permission::find($module_id);
        if ($module->permissions) {
            $module->permissions = json_decode($module->permissions);
        }
        return [
            'module' => $module
        ];
    }
    public function getModulePermissions($module)
    {
        $adminPermissions = RoleRepository::getModulePermissions('admin', $module);
        $selectedPermissions = [];
        if (\request('permission_group_id')) {
            $selectedPermissions = @Permission::where([
                ['permission_group_id', '=', \request('permission_group_id')],
                ['module', '=', $module]
            ])->first()->permissions;
            //            dd($selectedPermissions);
            if ($selectedPermissions) {
                $selectedPermissions = json_decode($selectedPermissions);
                //                $replaced = [];
                //                foreach ($selectedPermissions as $permission){
                //                    if($permission != $module){
                //                        $replaced[] = $permission;
                //                    }
                //                }
                //                $selectedPermissions = $replaced;
            }
        }
        return [
            'module' => $module,
            'permissions' => $adminPermissions,
            'selectedPermissions' => $selectedPermissions
        ];
    }

    public function setModulePermissions($id)
    {
        $module = Permission::find($id);
        $permissions = (array) \request('permissions');
        $permissions[] = $module->module;
        $module->permissions = $permissions;
        $roleRepo = new RoleRepository();
        $allowed_urls = $roleRepo->extractRoleUrls($module->module, $module->permissions, 'admin');
        $module->urls = $allowed_urls;
        $module->update();
        session()->put('permissions', null);
        session()->put('allowed_urls', null);
        return [
            'status' => 'success',
            'module' => $module
        ];
    }
    public function updateModulePermissionsWithSlug($id, $module)
    {
        $permissiongroup = PermissionGroup::findOrFail($id);
        $module = $permissiongroup->permissions()->where('module', $module)->firstOrCreate([
            'module' => $module
        ]);
        $permissions = \request('permissions');
        if (!count($permissions)) {
            $module->delete();
        } else {
            $module->permissions = $permissions;
            $roleRepo = new RoleRepository();
            $allowed_urls = $roleRepo->extractRoleUrls($module->module, $module->permissions, 'admin');
            $module->urls = $allowed_urls;
            $module->update();
        }
        return [
            'status' => 'success',
            'module' => $module,
            'PermissionGroupModules' => $permissiongroup->permissions()->pluck('module')->toArray()
        ];
    }
    public function removeModulePermissions($id)
    {
        $modulePermissionGroup = Permission::find($id);
        $permissiongroup = PermissionGroup::find($modulePermissionGroup->PermissionGroup_id);
        $modulePermissionGroup->delete();
        ShRepository::storeLog('remove_PermissionGroup_permission', "Removed permission $modulePermissionGroup->module from PermissionGroup#$permissiongroup->id", $permissiongroup);
        return response([
            'status' => 'success'
        ]);
    }
}
