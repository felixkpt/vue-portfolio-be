<?php

namespace App\Http\Controllers\Api\Admin\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\LeadNote;
use Iankibet\Shbackend\App\Repositories\SearchRepo;
use Iankibet\Shbackend\App\Repositories\ShRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class LeadNotesController extends Controller
{

     public function __construct()
        {
            $this->api_model = LeadNote::class;
        }
        public function storeLeadNote(){
            $data = \request()->all();
            $valid = Validator::make($data,ShRepository::getValidationRules($this->api_model));
            if (count($valid->errors())) {
                return response([
                    'status' => 'failed',
                    'errors' => $valid->errors()
                ], 422);
            }
            $data['form_model'] = encrypt($this->api_model);

            $leadnote = ShRepository::autoSaveModel($data);
            ShRepository::storeLog('store_leads_note',"Store Leads Notes # $leadnote->note", $leadnote);
            return [
              'status'=>'success',
              'leadnote'=>$leadnote
            ];
        }

        public function listSelfLeadNotes(){
            $user = \request()->user();
            $leadnotes = LeadNote::where('user_id',$user->id);
            $table = 'leadnotes';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadnotes,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function listAnyLeadNotes($id){
            $leadnotes =  LeadNote::where('lead_id', '=', $id);
            $table = 'leadnotes';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadnotes,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function getAnyLeadNote($id){
            $leadnote = LeadNote::find($id);
            return [
                'status'=>'success',
                'leadnote'=>$leadnote
            ];
        }
         public function getSelfLeadNote($id){
            $user = \request()->user();
            $leadnote = LeadNote::where('user_id',$user->id)->find($id);
            return [
                'status'=>'success',
                'leadnote'=>$leadnote
            ];
        }

}
