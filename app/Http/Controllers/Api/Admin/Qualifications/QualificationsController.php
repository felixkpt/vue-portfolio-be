<?php

namespace App\Http\Controllers\Api\Admin\Qualifications;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use App\Models\Qualification;
use Carbon\Carbon;

class QualificationsController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return qualifications's index view
     */
    public function index()
    {
        $qualifications = Qualification::with('user')->paginate();

        return response(['message' => 'success', 'data' => $qualifications]);
    }

    /**
     * store qualifications
     */
    public function store()
    {

        request()->validate([
            'institution' => 'required|unique:qualifications,institution,' . request()->id . ',id',
            'course' => 'required',
            'qualification' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = \request()->all();

        $data['start_date'] = Carbon::parse(request()->start_date)->format('Y-m');
        $data['end_date'] = Carbon::parse(request()->end_date)->format('Y-m');

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('qualifications', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
        }

        $res = Qualification::updateOrCreate(['id' => request()->id], $data);
        return response(['type' => 'success', 'message' => 'Qualification ' . $action . ' successfully', 'data' => $res], 201);
    }

    public function update()
    {
        return $this->store();
    }

    function show($id)
    {

        $res = Qualification::find($id);
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }
    /**
     * toggle qualifications status
     */
    public function changeStatus($id)
    {
        $qualification = Qualification::findOrFail($id);
        $state = $qualification->status == 1 ? 'Deactivated' : 'Activated';
        $qualification->status = $qualification->status == 1 ? 0 : 1;
        $qualification->save();
        return response(['type' => 'success', 'message' => 'Qualification #' . $qualification->id . ' has been ' . $state]);
    }

    /**
     * delete qualifications
     */
    public function destroyQualification($id)
    {
        $qualification = Qualification::findOrFail($id);
        $qualification->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Qualification deleted successfully']);
    }
}
