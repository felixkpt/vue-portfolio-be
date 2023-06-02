<?php

namespace App\Http\Controllers\Api\Client\Companies;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Models\Company;

class CompaniesController extends Controller
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
        $items = Company::wherestatus(1)->where('slug', '!=', 'self-projects')->orderby('start_date', 'desc');

        if (request()->all)
            return $items->get();

        $items = $items->paginate(request()->per_page);

        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Company::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
