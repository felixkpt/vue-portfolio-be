<?php

namespace App\Http\Controllers\Admin\Cats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Cat;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class CatsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return cat's index view
     */
    public function index() {
        return view($this->folder.'cat', []);
    }

    /**
     * store cat
     */
    public function storeCat() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('cats', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Cat '.$action.' successfully']);
    }

    /**
     * return cat values
     */
    public function listCats() {
        $cats = Cat::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('cats', 'status')) return $cats->where('status', 1)->get();
            else return $cats->get();
        }
        
        return SearchRepo::of($cats)
            ->addColumn('action', function($cat) {
                $str = '';
                $json = json_encode($cat);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'cat_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/cats/delete').'\',\''.$cat->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle cat status
     */
    public function toggleCatStatus($cat_id)
    {
        $cat = Cat::findOrFail($cat_id);        
        $state = $cat->status == 1 ? 'Deactivated' : 'Activated';
        $cat->status = $cat->status == 1 ? 0 : 1;
        $cat->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Cat #'.$cat->id.' has been '.$state]);
    }
    
    /**
     * delete cat
     */
    public function destroyCat($cat_id)
    {
        $cat = Cat::findOrFail($cat_id);
        $cat->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Cat deleted successfully']);
    }

}
