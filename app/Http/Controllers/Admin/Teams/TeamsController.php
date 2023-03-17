<?php

namespace App\Http\Controllers\Admin\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Team;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class TeamsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return team's index view
     */
    public function index() {
        return view($this->folder.'team', []);
    }

    /**
     * store team
     */
    public function storeTeam() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('teams', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Team '.$action.' successfully']);
    }

    /**
     * return team values
     */
    public function listTeams() {
        $teams = Team::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('teams', 'status')) return $teams->where('status', 1)->get();
            else return $teams->get();
        }
        
        return SearchRepo::of($teams)
            ->addColumn('action', function($team) {
                $str = '';
                $json = json_encode($team);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'team_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/teams/delete').'\',\''.$team->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle team status
     */
    public function toggleTeamStatus($team_id)
    {
        $team = Team::findOrFail($team_id);        
        $state = $team->status == 1 ? 'Deactivated' : 'Activated';
        $team->status = $team->status == 1 ? 0 : 1;
        $team->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Team #'.$team->id.' has been '.$state]);
    }
    
    /**
     * delete team
     */
    public function destroyTeam($team_id)
    {
        $team = Team::findOrFail($team_id);
        $team->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Team deleted successfully']);
    }

}
