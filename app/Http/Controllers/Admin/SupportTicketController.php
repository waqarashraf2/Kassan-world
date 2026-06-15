<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        return view('admin.support-tickets.index', [
            'tickets' => SupportTicket::latest()->paginate(30),
        ]);
    }

    public function show(SupportTicket $supportTicket)
    {
        return view('admin.support-tickets.show', ['ticket' => $supportTicket->load('user')]);
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $supportTicket->update($request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'priority' => ['required', 'in:low,normal,high'],
        ]));

        return back()->with('success', __('Support ticket updated.'));
    }
}
