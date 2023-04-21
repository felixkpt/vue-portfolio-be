<?php

namespace App\Http\Controllers\Api\Admin\Leads;

use App\Http\Controllers\Controller;
use App\Models\Core\Lead;
use App\Models\Core\LeadCategory;
use App\Models\Core\Tag;
use Illuminate\Http\Request;

use App\Models\Core\LeadTag;
use Iankibet\Shbackend\App\Repositories\SearchRepo;
use Iankibet\Shbackend\App\Repositories\ShRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class LeadTagsController extends Controller
{

     public function __construct()
        {
            $this->api_model = LeadTag::class;
        }
        public function tag(){
            $data = \request()->all();
            $valid = Validator::make($data,ShRepository::getValidationRules(Tag::class));
            if (count($valid->errors())) {
                return response([
                    'status' => 'failed',
                    'errors' => $valid->errors()
                ], 422);
            }
            $data['form_model'] = encrypt(Tag::class);
            $data['user_id'] = \request()->user()->id;
            $tag = ShRepository::autoSaveModel($data);
            ShRepository::storeLog('store_tag',"Store Leads Tag # $tag->name", $tag);

            return [
              'status'=>'success',
              'tag'=>$tag
            ];
        }

        public function listSelfLeadTags(){
            $user = \request()->user();
            $leadtags = LeadTag::where('user_id',$user->id);
            $table = 'leadtags';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadtags,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function listAnyTags(){
            $leadtags = Tag::join('lead_categories', 'tags.Lead_category_id', '=', 'lead_categories.id')
                                    ->select('tags.*', 'lead_categories.name as lead_category')
                                    ->where('tags.status', '!=', 2);
            $table = 'tags';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadtags,$table,$search_keys)
                    ->make(true)
            ];
        }

        public function updateTagStatus($id){
            $tag = Tag::find($id);
            $tag->status = 2;
            $tag->update();

            return [
                'status'=>'success',
                'tag'=>$tag
            ];
        }


        public function storeLeadTag(){
            $data = \request()->all();

            if (empty($data['tag_id'])){
                $lead = Lead::find($data['lead_id']);
                $lead_category_id = $lead->lead_category_id;

                $tag = new Tag();
                $tag->name = $data['tag'];
                $tag->lead_category_id = $lead_category_id;
                $tag->user_id = \request()->user()->id;
                $tag->save();
                ShRepository::storeLog('store_tag',"Store Leads Tag # $tag->name", $tag);
                $tag_id = $tag->id;
            } else {
                $tag_id = $data['tag_id'];
            }
            $data = [
                'lead_id' => $data['lead_id'],
                'tag' => $data['tag'],
                'user_id' => \request()->user()->id,
                'tag_id' => $tag_id,
            ];

            $valid = Validator::make($data,ShRepository::getValidationRules($this->api_model));
            if (count($valid->errors())) {
                return response([
                    'status' => 'failed',
                    'errors' => $valid->errors()
                ], 422);
            }
            $data['form_model'] = encrypt($this->api_model);
            $data['user_id'] = \request()->user()->id;
            $leadtag = ShRepository::autoSaveModel($data);
            ShRepository::storeLog('store_lead_tag',"Store Leads Lead Tag", $leadtag);

            return [
                'status'=>'success',
                'leadtag'=>$leadtag
            ];
        }

        public function listAnyLeadTags(){
            $leadtags = new LeadTag();
            $table = 'leadtags';
            $search_keys = array_keys(ShRepository::getValidationRules($this->api_model));
            return[
                'status'=>'success',
                'data'=>SearchRepo::of($leadtags,$table,$search_keys)
                    ->make(true)
            ];
        }
}
