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
     * return portfolio's index view
     */
    public function index()
    {
        if (request()->all == 1)
        return Project::where('status', 1)->get();
        
        $projects = Project::with(['company', 'skills'])->paginate();

        return response(['message' => 'success', 'data' => $projects]);
    }

    /**
     * store portfolio
     */
    public function store()
    {

        request()->validate([
            'title' => 'required|unique:projects,title,' . request()->id . ',id',
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
        }

        $res = Project::updateOrCreate(['id' => request()->id], $data);

        if (isset($data['skills']))
            $res->skills()->sync($data['skills']);

        return response(['type' => 'success', 'message' => 'Project ' . $action . ' successfully'], 201);
    }

    function show($id)
    {

        $res = Project::find($id)->with(['company', 'skills'])->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }
    /**
     * toggle portfolio status
     */
    public function toggleProjectStatus($portfolio_id)
    {
        $portfolio = Project::findOrFail($portfolio_id);
        $state = $portfolio->status == 1 ? 'Deactivated' : 'Activated';
        $portfolio->status = $portfolio->status == 1 ? 0 : 1;
        $portfolio->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Project #' . $portfolio->id . ' has been ' . $state]);
    }

    /**
     * delete portfolio
     */
    public function destroyProject($portfolio_id)
    {
        $portfolio = Project::findOrFail($portfolio_id);
        $portfolio->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Project deleted successfully']);
    }
}