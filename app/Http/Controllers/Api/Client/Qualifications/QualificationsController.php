<?php

namespace App\Http\Controllers\Api\Client\Qualifications;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Models\Qualification;

class QualificationsController extends Controller
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
        $items = Qualification::wherestatus(1)->with('user')->orderby('importance', 'desc')->paginate();
        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Qualification::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
