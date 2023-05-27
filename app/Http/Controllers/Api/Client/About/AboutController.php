<?php

namespace App\Http\Controllers\Api\Client\About;

use App\Http\Controllers\Controller;

use App\Models\About;
use App\Http\Traits\ControllerTrait;

class AboutController extends Controller
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
            return About::where('status', 'published')->get();

        $company = About::with('user')->paginate();

        return response(['message' => 'success', 'data' => $company]);
    }

    function show($id)
    {
        $item = About::wherestatus('published')->when(is_numeric($id), fn ($q) => $q->whereid($id))->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
