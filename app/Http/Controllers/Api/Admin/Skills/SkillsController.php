<?php

namespace App\Http\Controllers\Api\Admin\Skills;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\Skill;
use Carbon\Carbon;

class SkillsController extends Controller
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
        return Skill::where('status', 1)->orWhereNull('status')->get();

        $skills = Skill::with(['user', 'skillCategory'])->paginate(request()->per_page);

        return response(['message' => 'success', 'data' => $skills]);
    }

    /**
     * store skills
     */
    public function store()
    {

        request()->validate([
            'name' => 'required|unique:skills,name,' . request()->id . ',_id',
            'start_date' => 'required|date',
            'level' => 'required|string',
            'skills_category_id' => 'required|string'
        ]);

        $data = \request()->all();

        $data['start_date'] = Carbon::parse(request()->start_date)->format('Y-m');

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('skills', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 1;
        }

        $res = Skill::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);
        return response(['type' => 'success', 'message' => 'Skill ' . $action . ' successfully', 'data' => $res], $action == 'saved' ? 201 : 200);
    }

    public function update()
    {
        return $this->store();
    }

    function show($id)
    {

        $res = Skill::find($id);
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }
    /**
     * toggle skills status
     */
    public function changeStatus($id)
    {
        $skill = Skill::findOrFail($id);
        $state = $skill->status == 1 ? 'Deactivated' : 'Activated';
        $skill->status = $skill->status == 1 ? 0 : 1;
        $skill->save();
        return response(['type' => 'success', 'message' => 'Skill #' . $skill->id . ' has been ' . $state]);
    }

    /**
     * delete skills
     */
    public function destroySkill($id)
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Skill deleted successfully']);
    }
}
