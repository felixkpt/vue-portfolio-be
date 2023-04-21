<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Admin;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class AdminController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return admin's index view
     */
    public function index() {
        return view($this->folder.'admin', []);
    }

    /**
     * store admin
     */
    public function storeAdmin() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('admins', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Admin '.$action.' successfully']);
    }

    /**
     * return admin values
     */
    public function listAdmins() {
        $admins = Admin::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('admins', 'status')) return $admins->where('status', 1)->get();
            else return $admins->get();
        }
        
        return SearchRepo::of($admins)
            ->addColumn('action', function($admin) {
                $str = '';
                $json = json_encode($admin);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'admin_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/admins/delete').'\',\''.$admin->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle admin status
     */
    public function toggleAdminStatus($admin_id)
    {
        $admin = Admin::findOrFail($admin_id);        
        $state = $admin->status == 1 ? 'Deactivated' : 'Activated';
        $admin->status = $admin->status == 1 ? 0 : 1;
        $admin->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Admin #'.$admin->id.' has been '.$state]);
    }
    
    /**
     * delete admin
     */
    public function destroyAdmin($admin_id)
    {
        $admin = Admin::findOrFail($admin_id);
        $admin->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Admin deleted successfully']);
    }

}
