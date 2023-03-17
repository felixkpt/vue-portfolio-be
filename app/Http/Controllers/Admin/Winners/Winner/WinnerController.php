<?php

namespace App\Http\Controllers\Admin\Winners\Winner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Winner;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class WinnerController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return winner's index view
     */
    public function index() {
        return view($this->folder.'winner', []);
    }

    /**
     * store winner
     */
    public function storeWinner() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('winners', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Winner '.$action.' successfully']);
    }

    /**
     * return winner values
     */
    public function listWinners() {
        $winners = Winner::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('winners', 'status')) return $winners->where('status', 1)->get();
            else return $winners->get();
        }
        
        return SearchRepo::of($winners)
            ->addColumn('action', function($winner) {
                $str = '';
                $json = json_encode($winner);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'winner_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/winners/delete').'\',\''.$winner->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle winner status
     */
    public function toggleWinnerStatus($winner_id)
    {
        $winner = Winner::findOrFail($winner_id);        
        $state = $winner->status == 1 ? 'Deactivated' : 'Activated';
        $winner->status = $winner->status == 1 ? 0 : 1;
        $winner->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Winner #'.$winner->id.' has been '.$state]);
    }
    
    /**
     * delete winner
     */
    public function destroyWinner($winner_id)
    {
        $winner = Winner::findOrFail($winner_id);
        $winner->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Winner deleted successfully']);
    }

}
