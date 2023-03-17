<?php

namespace App\Http\Controllers\Admin\Doves\Dove;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Dove;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class DoveController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return dove's index view
     */
    public function index() {
        return view($this->folder.'dovecontroller', []);
    }

    /**
     * store dove
     */
    public function storeDove() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('doves', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Dove '.$action.' successfully']);
    }

    /**
     * return dove values
     */
    public function listDoves() {
        $doves = Dove::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('doves', 'status')) return $doves->where('status', 1)->get();
            else return $doves->get();
        }
        
        return SearchRepo::of($doves)
            ->addColumn('action', function($dove) {
                $str = '';
                $json = json_encode($dove);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'dove_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/doves/delete').'\',\''.$dove->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle dove status
     */
    public function toggleDoveStatus($dove_id)
    {
        $dove = Dove::findOrFail($dove_id);        
        $state = $dove->status == 1 ? 'Deactivated' : 'Activated';
        $dove->status = $dove->status == 1 ? 0 : 1;
        $dove->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Dove #'.$dove->id.' has been '.$state]);
    }
    
    /**
     * delete dove
     */
    public function destroyDove($dove_id)
    {
        $dove = Dove::findOrFail($dove_id);
        $dove->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Dove deleted successfully']);
    }

}
