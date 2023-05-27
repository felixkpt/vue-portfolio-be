<?php

namespace App\Http\Controllers\Api\Admin\About;

use App\Http\Controllers\Controller;

use App\Models\About;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use Illuminate\Support\Str;

class AboutController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return about's index view
     */
    public function index()
    {
        if (request()->all == 1)
            return About::where('status', 1)->orWhereNull('status')->get();

        $about = About::with('user')->paginate();

        return response(['message' => 'success', 'data' => $about]);
    }

    /**
     * store about
     */
    public function store()
    {
        // dd(request()->all());
        request()->validate([
            'salutation' => 'nullable|string',
            'name' => 'required|unique:about,name,' . request()->id.',_id',
            'content' => 'required|',
            'content_short' => 'required|string',
        ]);

        $data = \request()->all();

        $data['slug'] = Str::slug($data['name']);
        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('companies', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 'published';
        }

        About::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);

        return response(['type' => 'success', 'message' => 'About ' . $action . ' successfully']);
    }

    function update()
    {
        return $this->store();
    }

    function show($id)
    {
        $item = About::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }

    /**
     * change about status
     */
    public function changeStatus($id)
    {
        $about = About::findOrFail($id);
        $state = $about->status == 'published' ? 'Deactivated' : 'Activated';
        $about->status = $about->status == 'published' ? 'draft' : 'published';
        $about->save();
        return response(['type' => 'success', 'message' => 'About #' . $about->id . ' has been ' . $state]);
    }

    /**
     * delete about
     */
    public function destroy($id)
    {
        $about = About::findOrFail($id);
        $about->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'About deleted successfully']);
    }
}
