<?php

namespace App\Http\Controllers\Api\Admin\Projects;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProjectsController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return project's index view
     */
    public function index()
    {
        $items = Project::with(['user:name', 'company:name', 'skills'])->wherestatus('published')->orderby('importance', 'desc');

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

    /**
     * store project
     */
    public function store($is_update = false)
    {

        if (request()->id && !$is_update) abort(403);

        request()->validate([
            'title' => 'required|unique:projects,title,' . request()->id . ',_id',
            'company_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'skills' => 'required',
        ]);

        // $dom = new \DOMDocument();
        // $dom->loadHTML(request()->content);
        // $inlineAttachments = $dom->getElementsByTagName('img');

        // foreach( $inlineAttachments as $inlineAttachment ) {
        //     $src = $inlineAttachment->getAttribute('src'); // Attribute's value 
        // }

        $data = \request()->all();
        $data['start_date'] = Carbon::parse($data['start_date'])->format('Y-m');
        if (request()->end_date)
            $data['end_date'] = Carbon::parse($data['end_date'])->format('Y-m');

        $data['slug'] = Str::slug($data['title']);

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('projects', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 'published';
        }

        $res = Project::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);

        if (isset($data['skills']))
            $res->skills()->sync($data['skills']);

        $res = Project::with(['company', 'skills'])->find($res->_id);

        return response(['type' => 'success', 'message' => 'Project ' . $action . ' successfully', 'data' => $res], $action == 'saved' ? 201 : 200);
    }

    function update()
    {
        return $this->store(true);
    }
    function show($id)
    {
        $res = Project::with(['company', 'skills'])->find($id);
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }
    /**
     * toggle project status
     */
    public function toggleProjectStatus($project_id)
    {
        $project = Project::findOrFail($project_id);
        $state = $project->status == 'published' ? 'Deactivated' : 'Activated';
        $project->status = $project->status == 'published' ? 'draft' : 'published';
        $project->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Project #' . $project->id . ' has been ' . $state]);
    }

    /**
     * delete project
     */
    public function destroyProject($project_id)
    {
        $project = Project::findOrFail($project_id);
        $project->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Project deleted successfully']);
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
