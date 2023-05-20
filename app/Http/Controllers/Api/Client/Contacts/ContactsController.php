<?php

namespace App\Http\Controllers\Api\Client\Contacts;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Models\Contact;

class ContactsController extends Controller
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
        $items = Contact::wherestatus(1)->with('user')->paginate();
        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Contact::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }
}
