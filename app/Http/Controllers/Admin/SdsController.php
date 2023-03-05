<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Sd;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class SdsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return sd's index view
     */
    public function index() {
        return view($this->folder.'sd', []);
    }

    /**
     * store sd
     */
    public function storeSd() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('sds', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Sd '.$action.' successfully']);
    }

    /**
     * return sd values
     */
    public function listSds() {
        $sds = Sd::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('sds', 'status')) return $sds->where('status', 1)->get();
            else return $sds->get();
        }
        
        return SearchRepo::of($sds)
            ->addColumn('action', function($sd) {
                $str = '';
                $json = json_encode($sd);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'sd_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/sds/delete').'\',\''.$sd->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle sd status
     */
    public function toggleSdStatus($sd_id)
    {
        $sd = Sd::findOrFail($sd_id);        
        $state = $sd->status == 1 ? 'Deactivated' : 'Activated';
        $sd->status = $sd->status == 1 ? 0 : 1;
        $sd->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Sd #'.$sd->id.' has been '.$state]);
    }
    
    /**
     * delete sd
     */
    public function destroySd($sd_id)
    {
        $sd = Sd::findOrFail($sd_id);
        $sd->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Sd deleted successfully']);
    }

}
