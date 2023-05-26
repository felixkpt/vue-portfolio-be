<?php

namespace App\Http\Controllers\Api\Admin\Contacts;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema;
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
        $contacts = Contact::with('user')->paginate();

        return response(['message' => 'success', 'data' => $contacts]);
    }

    /**
     * store contacts
     */
    public function store()
    {

        request()->validate([
            'type' => 'required|unique:contacts,type,' . request()->id . ',_id',
            'link' => 'required',
        ]);

        $data = \request()->all();

        if (!isset($data['user_id'])) {
            if (Schema::hasColumn('contacts', 'user_id'))
                $data['user_id'] = currentUser()->id;
        }

        if (\request()->id) {
            $action = "updated";
        } else {
            $action = "saved";
            $data['status'] = 1;
        }

        $res = Contact::updateOrCreate(['_id' => request()->id ?? str()->random(20)], $data);
        return response(['type' => 'success', 'message' => 'Contact ' . $action . ' successfully', 'data' => $res], 201);
    }

    public function update()
    {
        return $this->store();
    }

    function show($id)
    {

        $res = Contact::find($id);
        return response(['type' => 'success', 'message' => 'successfully', 'data' => $res], 200);
    }
    /**
     * toggle contacts status
     */
    public function changeStatus($id)
    {
        $contact = Contact::findOrFail($id);
        $state = $contact->status == 1 ? 'Deactivated' : 'Activated';
        $contact->status = $contact->status == 1 ? 0 : 1;
        $contact->save();
        return response(['type' => 'success', 'message' => 'Contact #' . $contact->id . ' has been ' . $state]);
    }

    /**
     * delete contacts
     */
    public function destroyContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Contact deleted successfully']);
    }
}
