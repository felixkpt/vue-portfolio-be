<?php

namespace App\Http\Controllers\Api\Client\SkillsCategories;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Models\SkillsCategory;

class SkillsCategoriesController extends Controller
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
        $items = SkillsCategory::wherestatus(1)->with(['user', 'skills'])->paginate(request()->per_page);
        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = SkillsCategory::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
