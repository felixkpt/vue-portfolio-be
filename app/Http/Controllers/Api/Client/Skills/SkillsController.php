<?php

namespace App\Http\Controllers\Api\Client\Skills;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Models\Skill;

class SkillsController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return contacts's index view
     */
    public function index()
    {
        $items = Skill::wherestatus(1)->with(['user', 'skill_category'])->orderby('importance', 'desc')->paginate();
        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Skill::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
