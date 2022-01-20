<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactImportRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Imports\ContactsImport;
use App\Jobs\SendContactJob;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the contacts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $contacts = Contact::where('user_id', auth()->user()->id);

            if ($request->search['value']) {
                $contacts = $contacts->where('name', 'like', '%' . $request->search['value'] . '%')
                    ->Orwhere('email', 'like', '%' . $request->search['value'] . '%')
                    ->Orwhere('phone', 'like', '%' . $request->search['value'] . '%');
            }

            $order = $request->columns[$request->order[0]['column']]['name'];
            $dir   = $request->order[0]['dir'];

            $contacts = $contacts->orderBy($order, $dir);

            $offset        = $request->start ?: 0;
            $limit         = $request->length ?: 10;
            $contacts      = $contacts->orderBy('id', 'desc');
            $contactsTotal = $contacts->get();
            $contacts      = $contacts->skip($offset)->take($limit)->get();
            $contactList   = [];

            if ($contacts) {
                foreach ($contacts as $contact) {
                    $currentData   = [];
                    $currentData[] = '<div class="checkbox-inline"><div class="checkbox-inline">' .
                        '<input type="checkbox" class="checkbox-inline chkbox" name="id" data-id="'.$contact->id.'">' . '
                        <label for="id' . $contact->id . '"></label></div></div>';
                    $currentData[] = $contact->id;
                    $currentData[] = $contact->name;
                    $currentData[] = $contact->email;
                    $currentData[] = $contact->phone;
                    $currentData[] = '<div class="d-flex"> 
                                        <a href="'.route('contact.edit', $contact->id) .'" class="btn btn-primary" style="margin-right: 10px">Edit</a>
                                        <a href="javascript:void(0);" onClick="deleteRecord('.$contact->id.')" class="btn btn-danger">Delete</a> 
                                      </div>';
                    $contactList[] = $currentData;
                }
            }

            $json_data = [
                "draw"            => intval($request['draw']),
                "data"            => $contactList,
                "recordsTotal"    => count($contactsTotal),
                "recordsFiltered" => count($contactsTotal)
            ];
            return response()->json($json_data);
        }
        return view('contacts.index');
    }

    /**
     * Show the form for creating a new contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created contact in storage.
     *
     * @param  \App\Http\Requests\ContactCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactCreateRequest $request)
    {
        if (request()->ajax()) {

            $contact = Contact::create([
                'user_id' => auth()->user()->id,
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone
            ]);

            if ($contact->id !='') {
                dispatch(new SendContactJob($contact, auth()->user()->token));
            }

            $request->session()->flash('success', 'Contact created Successfully');

            return response()->json([
                'message' => 'Contact created successfully',
                'status'  => 'success',
                'code'    => 200
            ]);
        } else {
            return response()->json([
                'message' => 'You can not access this page.',
                'status'  => false,
                'code'    => 404
            ]);
        }
    }

    /**
     * Show the form for editing the specified contact.
     *
     * @param  \App\Models\Contact int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($id !='') {
            $contact = Contact::findOrFail($id);
            if ($contact) {
                return view('contacts.update', [
                    'contact' => $contact
                ]);
            }
        }
        return response()->json([
            'message' => 'You can not access this page.',
            'status'  => false,
            'code'    => 404
        ]);
    }

    /**
     * Update the specified resource in contact.
     *
     * @param  \App\Http\Requests\ContactUpdateRequest $request
     * @param  \App\Models\Contact int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactUpdateRequest $request, $id)
    {
        if (request()->ajax()) {
            $contact = Contact::findOrFail($id);
            if ($contact) {
                $contact->name  = $request->name;
                $contact->email = $request->email;
                $contact->phone = $request->phone;
                $contact->save();
                if ($contact->save()) {
                    dispatch(new SendContactJob($contact,  auth()->user()->token));
                }

                $request->session()->flash('success', 'Contact updated successfully');

                return response()->json([
                    'message' => 'Contact updated successfully',
                    'status'  => 'success',
                    'code'    => 200
                ]);
            }
        }
        return response()->json([
            'message' => 'You can not access this page.',
            'status'  => false,
            'code'    => 404
        ]);
    }

    /**
     * Remove the specified contact from storage.
     *
     * @param  \App\Models\Contact int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            $contact = Contact::findOrFail($id);
            if ($contact) {
                $contact->delete();

                return response()->json([
                    'message' => 'Contact has been deleted successfully',
                    'status'  => 'success',
                    'code'    => 200
                ]);
            }
        }
        return response()->json([
            'message' => 'You can not access this page.',
            'status'  => false,
            'code'    => 404
        ]);
    }

    /**
     * Remove the specified contact from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if (request()->ajax()) {
            if ($request->contact_ids) {
                collect($request->contact_ids)->map(function ($contact_id) {
                    $contact = Contact::find($contact_id);
                    $contact->delete();
                });

                return response()->json([
                    'message' => 'Contact has been deleted successfully',
                    'status'  => 'success',
                    'code'    => 200
                ]);
            }
        }
        return response()->json([
            'message' => 'You can not access this page.',
            'status'  => false,
            'code'    => 404
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import()
    {
        return view('contacts.import');
    }

    public function importContacts(ContactImportRequest $request)
    {
        (new ContactsImport())->import(request()->file('file'));
        return redirect()->route('contact.index');
    }
}


