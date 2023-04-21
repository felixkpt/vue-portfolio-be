<?php

namespace App\Http\Controllers\Api\Admin\Leads;

use App\Http\Controllers\Controller;
use App\Models\Core\Category;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;

use App\Models\Core\LeadCategory;
use App\Repositories\SearchRepo;
use App\Cih\Repositories\ShRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class LeadCategoriesController extends Controller
{

     public function __construct()
        {
            $this->api_model = LeadCategory::class;
        }
        public function storeLeadCategory(){
            $data = \request()->all();
            $valid = Validator::make($data,ShRepository::getValidationRules($this->api_model));
            if (count($valid->errors())) {
                return response([
                    'status' => 'failed',
                    'errors' => $valid->errors()
                ], 422);
            }
            
            
            $data['user_id'] = currentUser()->id;
            $leadcategory = ShRepository::autoSaveModel($this->api_model, $data);
            ShRepository::storeLog('store_leads_category',"Store Leads Category # $leadcategory->name", $leadcategory);
            return [
              'status'=>'success',
              'leadcategory'=>$leadcategory
            ];
        }

        public function listSelfLeadCategorys(){
            $user = \request()->user();
            $leadcategorys = LeadCategory::where('user_id',$user->id);
            $table = 'lead_categories';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadcategorys,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function listAnyLeadCategories(){
            $leadcategorys = LeadCategory::where('status', '!=', 2);
            $table = 'lead_categories';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadcategorys,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function getAnyLeadCategory($id){
            $leadcategory = LeadCategory::find($id);
            return [
                'status'=>'success',
                'leadcategory'=>$leadcategory
            ];
        }
         public function getSelfLeadCategory($id){
            $user = \request()->user();
            $leadcategory = LeadCategory::where('user_id',$user->id)->find($id);
            return [
                'status'=>'success',
                'leadcategory'=>$leadcategory
            ];
        }

    public function updateCategory($id){
        $lead_category = LeadCategory::find($id);
        $lead_category->status = 2;
        $lead_category->update();
        return response([
            'status' => 'success',
            'cateogry' => $lead_category
          ]);
        }

}
