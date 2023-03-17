<?php

namespace App\Http\Controllers\Admin\Sons;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Son;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class SonsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return son's index view
     */
    public function index() {
        return view($this->folder.'sons', []);
    }

    /**
     * store son
     */
    public function storeSon() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('sons', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Son '.$action.' successfully']);
    }

    /**
     * return son values
     */
    public function listSons() {
        $sons = Son::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('sons', 'status')) return $sons->where('status', 1)->get();
            else return $sons->get();
        }
        
        return SearchRepo::of($sons)
            ->addColumn('action', function($son) {
                $str = '';
                $json = json_encode($son);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'son_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/sons/delete').'\',\''.$son->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle son status
     */
    public function toggleSonStatus($son_id)
    {
        $son = Son::findOrFail($son_id);        
        $state = $son->status == 1 ? 'Deactivated' : 'Activated';
        $son->status = $son->status == 1 ? 0 : 1;
        $son->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Son #'.$son->id.' has been '.$state]);
    }
    
    /**
     * delete son
     */
    public function destroySon($son_id)
    {
        $son = Son::findOrFail($son_id);
        $son->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Son deleted successfully']);
    }

}
