<?php

namespace App\Http\Controllers\Api\Admin\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Lead;
use Iankibet\Shbackend\App\Repositories\SearchRepo;
use Iankibet\Shbackend\App\Repositories\ShRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class LeadsController extends Controller
{

     public function __construct()
        {
            $this->api_model = Lead::class;
        }
        public function storeLead(){
            $data = \request()->all();
            $valid = Validator::make($data,ShRepository::getValidationRules($this->api_model));
            if (count($valid->errors())) {
                return response([
                    'status' => 'failed',
                    'errors' => $valid->errors()
                ], 422);
            }
            $data['form_model'] = encrypt($this->api_model);
            //$data['user_id'] = \request()->user()->id;
            $lead = ShRepository::autoSaveModel($data);
            ShRepository::storeLog('store_lead',"Store Leads # $lead->name", $lead);

            return [
              'status'=>'success',
              'leads'=>$lead
            ];
        }

        public function listSelfLeads(){
            $user = \request()->user();
            $leads = Lead::where('user_id',$user->id);
            $table = 'leads';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leads,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function listAnyLeads(){
            $leads = Lead::join('lead_categories', 'leads.lead_category_id', '=', 'lead_categories.id')
                                ->select('leads.*', 'lead_categories.name as category');
            $table = 'leads';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leads,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function getAnyLead($id){
            $lead = Lead::find($id);
            return [
                'status'=>'success',
                'leads'=>$lead
            ];
        }
         public function getSelfLead($id){
            $user = \request()->user();
            $lead = Lead::where('user_id',$user->id)->find($id);
            return [
                'status'=>'success',
                'leads'=>$lead
            ];
        }

}
