<?php

namespace App\Http\Controllers\Admin\Hens\Hen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Hen;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class HenController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return hen's index view
     */
    public function index() {
        return view($this->folder.'hens', []);
    }

    /**
     * store hen
     */
    public function storeHen() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('hens', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Hen '.$action.' successfully']);
    }

    /**
     * return hen values
     */
    public function listHens() {
        $hens = Hen::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('hens', 'status')) return $hens->where('status', 1)->get();
            else return $hens->get();
        }
        
        return SearchRepo::of($hens)
            ->addColumn('action', function($hen) {
                $str = '';
                $json = json_encode($hen);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'hen_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/hens/delete').'\',\''.$hen->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle hen status
     */
    public function toggleHenStatus($hen_id)
    {
        $hen = Hen::findOrFail($hen_id);        
        $state = $hen->status == 1 ? 'Deactivated' : 'Activated';
        $hen->status = $hen->status == 1 ? 0 : 1;
        $hen->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Hen #'.$hen->id.' has been '.$state]);
    }
    
    /**
     * delete hen
     */
    public function destroyHen($hen_id)
    {
        $hen = Hen::findOrFail($hen_id);
        $hen->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Hen deleted successfully']);
    }

}
