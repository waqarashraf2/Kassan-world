<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactReceivedMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        if ($contact->email) {
            try {
                Mail::to($contact->email)->send(new ContactReceivedMail($contact));
            } catch (\Throwable $exception) {
                Log::error('Contact auto-reply could not be sent.', ['contact' => $contact->id, 'error' => $exception->getMessage()]);
            }
        }

        return back()->with('success', __('Thank you. We will contact you soon.'));
    }
}
