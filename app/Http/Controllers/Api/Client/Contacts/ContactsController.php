<?php

namespace App\Http\Controllers\Api\Client\Contacts;

use App\Http\Controllers\Controller;

use App\Http\Traits\ControllerTrait;
use App\Mail\ContactMail;
use App\Mail\Contacts\AutoRespond;
use App\Mail\Contacts\SendMessage;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

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
        $items = Contact::wherestatus(1)->get();
        return response(['message' => 'success', 'data' => $items]);
    }

    function show($id)
    {
        $item = Contact::wherestatus(1)->whereid($id)->first();
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $item], 200);
    }

    function sendMessage()
    {

        request()->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $data = request()->all();
        Mail::to(config('mail.from.address'))->send(new SendMessage($data));
        Mail::to($data['email'])->send(new AutoRespond($data));

        return response(['type' => 'success', 'message' => 'Message sent successfully!']);
    }
}
