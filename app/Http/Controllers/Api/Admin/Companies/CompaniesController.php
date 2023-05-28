<?php

namespace App\Http\Controllers\Api\Admin\Companies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompaniesController extends Controller
{

    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return company's index view
     */
    public function index()
    {
        if (request()->all == 1)
            return Company::where('status', 1)->orWhereNull('status')->get();

        $company = Company::with('user')->paginate();

        return response(['message' => 'success', 'data' => $company]);
    }

    /**
     * store company
     */
    public function store($is_update = false)
    {
        if (request()->id && !$is_update) abort(403);

        request()->validate([
            'name' => 'required|unique:companies,name,' . request()->id . ',_id',
            'url' => 'required|url|unique:companies,url,' . request()->id . ',_id',
            'logo' => 'required|string',
            'position' => 'required|string',
            'roles' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $data = \request()->all();
        $data['start_date'] = Carbon::parse($data['start_date'])->format('Y-m');
        if (request()->end_date)
            $data['end_date'] = Carbon::parse($data['end_date'])->format('Y-m');


        $data['slug'] = Str::slug($data['name']);
        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('companies', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 1;
        }

        Company::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);

        return response(['type' => 'success', 'message' => 'Company ' . $action . ' successfully']);
    }

    function update()
    {
        return $this->store(true);
    }

    /**
     * toggle company status
     */
    public function changeStatus($id)
    {
        $company = Company::findOrFail($id);
        $state = $company->status == 1 ? 'Deactivated' : 'Activated';
        $company->status = $company->status == 1 ? 0 : 1;
        $company->save();
        return response(['type' => 'success', 'message' => 'Company #' . $company->id . ' has been ' . $state]);
    }

    /**
     * delete company
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Company deleted successfully']);
    }
}
