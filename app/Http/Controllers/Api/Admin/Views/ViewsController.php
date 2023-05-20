<?php

namespace App\Http\Controllers\Api\Admin\Views;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cow;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class ViewsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return cow's index view
     */
    public function index() {
        return view($this->folder.'index', []);
    }

    /**
     * store cow
     */
    public function store() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('cows', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Cow '.$action.' successfully']);
    }

    /**
     * return cow values
     */
    public function list() {
        $cows = Cow::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('cows', 'status')) return $cows->where('status', 1)->get();
            else return $cows->get();
        }
        
        return $cows = Cow::where([])->paginate(request()->perPage ?? 20);
    }

    /**
     * toggle cow status
     */
    public function toggleStatus($id)
    {
        $cow = Cow::findOrFail($id);        
        $state = $cow->status == 1 ? 'Deactivated' : 'Activated';
        $cow->status = $cow->status == 1 ? 0 : 1;
        $cow->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Cow #'.$cow->id.' has been '.$state]);
    }
    
    /**
     * delete cow
     */
    public function destroy($id)
    {
        $cow = Cow::findOrFail($id);
        $cow->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Cow deleted successfully']);
    }

}
