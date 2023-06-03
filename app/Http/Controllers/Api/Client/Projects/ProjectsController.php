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
        $items = Project::wherestatus('published')->with(['company:name', 'skills'])->orderby('importance', 'desc');

        if (request()->all) {
            $items = $items->limit(request()->per_page ?? 4)->get();
            return $this->select($items, 'skills', ['name']);
        }

        $items = $items->paginate(request()->per_page);

        $items_only = $items->getCollection();
        $res = $this->select($items_only, 'skills', ['name']);
        $items->setCollection($res);

        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Project::with(['company', 'skills'])->wherestatus('published')->whereslug($id)->firstOrFail();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }

    private function select($q, $relation, $columns)
    {
        return $q->map(
            function ($q) use ($relation, $columns) {
                unset($q[$relation]);
                $q[$relation] = $q->$relation()->get($columns);
                return $q;
            }
        );
    }
}
