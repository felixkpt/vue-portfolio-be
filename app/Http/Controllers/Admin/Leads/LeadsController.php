<?php

namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Lead;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class LeadsController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return lead's index view
     */
    public function index() {
        return view($this->folder.'lead', []);
    }

    /**
     * store lead
     */
    public function storeLead() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('leads', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Lead '.$action.' successfully']);
    }

    /**
     * return lead values
     */
    public function listLeads() {
        $leads = Lead::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('leads', 'status')) return $leads->where('status', 1)->get();
            else return $leads->get();
        }
        
        return SearchRepo::of($leads)
            ->addColumn('action', function($lead) {
                $str = '';
                $json = json_encode($lead);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'lead_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/leads/delete').'\',\''.$lead->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle lead status
     */
    public function toggleLeadStatus($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);        
        $state = $lead->status == 1 ? 'Deactivated' : 'Activated';
        $lead->status = $lead->status == 1 ? 0 : 1;
        $lead->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Lead #'.$lead->id.' has been '.$state]);
    }
    
    /**
     * delete lead
     */
    public function destroyLead($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);
        $lead->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Lead deleted successfully']);
    }

}
