<?php

namespace App\Http\Controllers\Admin\Demos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Demo;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class DemosController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return demo's index view
     */
    public function index() {
        return view($this->folder.'demo', []);
    }

    /**
     * store demo
     */
    public function storeDemo() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('demos', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Demo '.$action.' successfully']);
    }

    /**
     * return demo values
     */
    public function listDemos() {
        $demos = Demo::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('demos', 'status')) return $demos->where('status', 1)->get();
            else return $demos->get();
        }
        
        return SearchRepo::of($demos)
            ->addColumn('action', function($demo) {
                $str = '';
                $json = json_encode($demo);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'demo_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/demos/delete').'\',\''.$demo->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle demo status
     */
    public function toggleDemoStatus($demo_id)
    {
        $demo = Demo::findOrFail($demo_id);        
        $state = $demo->status == 1 ? 'Deactivated' : 'Activated';
        $demo->status = $demo->status == 1 ? 0 : 1;
        $demo->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Demo #'.$demo->id.' has been '.$state]);
    }
    
    /**
     * delete demo
     */
    public function destroyDemo($demo_id)
    {
        $demo = Demo::findOrFail($demo_id);
        $demo->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Demo deleted successfully']);
    }

}
