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
        $items = Project::with(['user:name', 'company:name', 'skills'])->wherestatus('published')->orderby('importance', 'desc');

        if (request()->all)
            return $items->limit(4)->get();

        $items = $items->paginate();

        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Project::with(['company', 'skills'])->wherestatus('published')->whereslug($id)->firstOrFail();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }

    private function select($q)
    {
        return $q->map(
            function ($q) {
                return [
                    ...$q->only([
                        '_id',
                        'title',
                        'slug',
                        'content_short',
                        'featured_image',
                    ]),
                    'company' => $q->company()->first(['name']),
                    'skills' => $q->skills()->get(['name'])
                ];
            }
        );
    }
}
