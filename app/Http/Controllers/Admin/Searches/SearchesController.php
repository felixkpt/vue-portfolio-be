<?php

namespace App\Http\Controllers\Admin\Searches;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Search;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class SearchesController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return search's index view
     */
    public function index() {
        return view($this->folder.'searches', []);
    }

    /**
     * store search
     */
    public function storeSearch() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('searches', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Search '.$action.' successfully']);
    }

    /**
     * return search values
     */
    public function listSearches() {
        $searches = Search::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('searches', 'status')) return $searches->where('status', 1)->get();
            else return $searches->get();
        }
        
        return SearchRepo::of($searches)
            ->addColumn('action', function($search) {
                $str = '';
                $json = json_encode($search);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'search_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/searches/delete').'\',\''.$search->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle search status
     */
    public function toggleSearchStatus($search_id)
    {
        $search = Search::findOrFail($search_id);        
        $state = $search->status == 1 ? 'Deactivated' : 'Activated';
        $search->status = $search->status == 1 ? 0 : 1;
        $search->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Search #'.$search->id.' has been '.$state]);
    }
    
    /**
     * delete search
     */
    public function destroySearch($search_id)
    {
        $search = Search::findOrFail($search_id);
        $search->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Search deleted successfully']);
    }

}
