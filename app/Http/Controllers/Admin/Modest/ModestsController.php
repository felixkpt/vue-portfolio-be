<?php

namespace App\Http\Controllers\Admin\Modest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Modest;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class ModestsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return modest's index view
     */
    public function index() {
        return view($this->folder.'modest', []);
    }

    /**
     * store modest
     */
    public function storeModest() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('modests', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Modest '.$action.' successfully']);
    }

    /**
     * return modest values
     */
    public function listModests() {
        $modests = Modest::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('modests', 'status')) return $modests->where('status', 1)->get();
            else return $modests->get();
        }
        
        return SearchRepo::of($modests)
            ->addColumn('action', function($modest) {
                $str = '';
                $json = json_encode($modest);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'modest_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/modests/delete').'\',\''.$modest->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle modest status
     */
    public function toggleModestStatus($modest_id)
    {
        $modest = Modest::findOrFail($modest_id);        
        $state = $modest->status == 1 ? 'Deactivated' : 'Activated';
        $modest->status = $modest->status == 1 ? 0 : 1;
        $modest->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Modest #'.$modest->id.' has been '.$state]);
    }
    
    /**
     * delete modest
     */
    public function destroyModest($modest_id)
    {
        $modest = Modest::findOrFail($modest_id);
        $modest->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Modest deleted successfully']);
    }

}
