<?php

namespace App\Http\Controllers\Admin\Mosquitos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Mosquito;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class MosquitosController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return mosquito's index view
     */
    public function index() {
        return view($this->folder.'', []);
    }

    /**
     * store mosquito
     */
    public function storeMosquito() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('mosquitos', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Mosquito '.$action.' successfully']);
    }

    /**
     * return mosquito values
     */
    public function listMosquitos() {
        $mosquitos = Mosquito::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('mosquitos', 'status')) return $mosquitos->where('status', 1)->get();
            else return $mosquitos->get();
        }
        
        return SearchRepo::of($mosquitos)
            ->addColumn('action', function($mosquito) {
                $str = '';
                $json = json_encode($mosquito);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'mosquito_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/mosquitos/delete').'\',\''.$mosquito->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle mosquito status
     */
    public function toggleMosquitoStatus($mosquito_id)
    {
        $mosquito = Mosquito::findOrFail($mosquito_id);        
        $state = $mosquito->status == 1 ? 'Deactivated' : 'Activated';
        $mosquito->status = $mosquito->status == 1 ? 0 : 1;
        $mosquito->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Mosquito #'.$mosquito->id.' has been '.$state]);
    }
    
    /**
     * delete mosquito
     */
    public function destroyMosquito($mosquito_id)
    {
        $mosquito = Mosquito::findOrFail($mosquito_id);
        $mosquito->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Mosquito deleted successfully']);
    }

}
