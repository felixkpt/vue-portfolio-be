<?php

namespace App\Http\Controllers\Admin\Cravings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Craving;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class CravingsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return craving's index view
     */
    public function index() {
        return view($this->folder.'craving', []);
    }

    /**
     * store craving
     */
    public function storeCraving() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('cravings', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Craving '.$action.' successfully']);
    }

    /**
     * return craving values
     */
    public function listCravings() {
        $cravings = Craving::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('cravings', 'status')) return $cravings->where('status', 1)->get();
            else return $cravings->get();
        }
        
        return SearchRepo::of($cravings)
            ->addColumn('action', function($craving) {
                $str = '';
                $json = json_encode($craving);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'craving_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/cravings/delete').'\',\''.$craving->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle craving status
     */
    public function toggleCravingStatus($craving_id)
    {
        $craving = Craving::findOrFail($craving_id);        
        $state = $craving->status == 1 ? 'Deactivated' : 'Activated';
        $craving->status = $craving->status == 1 ? 0 : 1;
        $craving->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Craving #'.$craving->id.' has been '.$state]);
    }
    
    /**
     * delete craving
     */
    public function destroyCraving($craving_id)
    {
        $craving = Craving::findOrFail($craving_id);
        $craving->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Craving deleted successfully']);
    }

}
