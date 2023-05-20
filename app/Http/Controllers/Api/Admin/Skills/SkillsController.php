<?php

namespace App\Http\Controllers\Api\Admin\Skills;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
        return Skill::where('status', 1)->get();

        $skills = Skill::with('user')->paginate();

        return response(['message' => 'success', 'data' => $skills]);
    }

    /**
     * store skills
     */
    public function store()
    {

        request()->validate([
            'name' => 'required|unique:skills,name,' . request()->id . ',id',
            'start_date' => 'required|date',
            'level' => 'required|string',
            'category' => 'required|string'
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
        }

        $res = Skill::updateOrCreate(['id' => request()->id], $data);
        return response(['type' => 'success', 'message' => 'Skill ' . $action . ' successfully', 'data' => $res], 201);
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
    public function toggleStatus($id)
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
