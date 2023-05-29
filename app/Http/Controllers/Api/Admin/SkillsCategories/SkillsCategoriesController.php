<?php

namespace App\Http\Controllers\Api\Admin\SkillsCategories;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\SkillsCategory;
use Carbon\Carbon;

class SkillsCategoriesController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return skills's index view
     */
    public function index()
    {
        if (request()->all == 1)
            return SkillsCategory::where('status', 1)->orWhereNull('status')->get();

        $skills = SkillsCategory::with('user')->paginate();

        return response(['message' => 'success', 'data' => $skills]);
    }

    /**
     * store skills
     */
    public function store()
    {

        request()->validate([
            'name' => 'required|unique:skills_categories,name,' . request()->id . ',_id',
        ]);

        $data = \request()->all();

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('skills_categories', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 1;
        }

        $res = SkillsCategory::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);
        $res->touch();
        return response(['type' => 'success', 'message' => 'SkillsCategory ' . $action . ' successfully', 'data' => $res], $action == 'saved' ? 201 : 200);
    }

    public function update()
    {
        return $this->store();
    }

    function show($id)
    {

        $res = SkillsCategory::find($id);
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }
    /**
     * toggle skills status
     */
    public function changeStatus($id)
    {
        $skill = SkillsCategory::findOrFail($id);
        $state = $skill->status == 1 ? 'Deactivated' : 'Activated';
        $skill->status = $skill->status == 1 ? 0 : 1;
        $skill->save();
        return response(['type' => 'success', 'message' => 'SkillsCategory #' . $skill->id . ' has been ' . $state]);
    }

    /**
     * delete skills
     */
    public function destroySkillsCategory($id)
    {
        $skill = SkillsCategory::findOrFail($id);
        $skill->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'SkillsCategory deleted successfully']);
    }
}
