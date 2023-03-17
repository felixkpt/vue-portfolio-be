<?php

namespace App\Http\Controllers\Admin\Cows\Cow;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Dog;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class CowController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return dog's index view
     */
    public function index() {
        return view($this->folder.'dogs', []);
    }

    /**
     * store dog
     */
    public function storeDog() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('dogs', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Dog '.$action.' successfully']);
    }

    /**
     * return dog values
     */
    public function listDogs() {
        $dogs = Dog::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('dogs', 'status')) return $dogs->where('status', 1)->get();
            else return $dogs->get();
        }
        
        return SearchRepo::of($dogs)
            ->addColumn('action', function($dog) {
                $str = '';
                $json = json_encode($dog);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'dog_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/dogs/delete').'\',\''.$dog->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle dog status
     */
    public function toggleDogStatus($dog_id)
    {
        $dog = Dog::findOrFail($dog_id);        
        $state = $dog->status == 1 ? 'Deactivated' : 'Activated';
        $dog->status = $dog->status == 1 ? 0 : 1;
        $dog->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Dog #'.$dog->id.' has been '.$state]);
    }
    
    /**
     * delete dog
     */
    public function destroyDog($dog_id)
    {
        $dog = Dog::findOrFail($dog_id);
        $dog->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Dog deleted successfully']);
    }

}
