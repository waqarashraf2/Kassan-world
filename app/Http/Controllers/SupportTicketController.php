<?php

namespace App\Http\Controllers;

use App\Mail\SupportTicketReceivedMail;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
            'priority' => ['nullable', 'in:low,normal,high'],
        ]);

        $ticket = SupportTicket::create($data + [
            'ticket_number' => 'KWT-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
            'user_id' => $request->user()?->id,
        ]);

        try {
            Mail::to($ticket->email)->send(new SupportTicketReceivedMail($ticket));
        } catch (\Throwable $exception) {
            Log::error('Support ticket auto-reply could not be sent.', ['ticket' => $ticket->id, 'error' => $exception->getMessage()]);
        }

        return back()->with('success', __('Support ticket :number created.', ['number' => $ticket->ticket_number]));
    }
}
