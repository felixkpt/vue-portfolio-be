<?php

namespace App\Http\Controllers\Api\Client\Projects;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Models\Project;

class ProjectsController extends Controller
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
        $items = Project::wherestatus('published')->with(['company', 'skills']);

        if (request()->all)
            return $items->get();

        $items = $items->paginate();

        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Project::with(['company', 'skills'])->wherestatus('published')->whereslug($id)->firstOrFail();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
