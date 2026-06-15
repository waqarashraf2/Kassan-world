<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->update(['chat_available' => true, 'last_seen_at' => now()]);

        return view('admin.chats.index', [
            'conversations' => ChatConversation::with(['user', 'assignedAdmin'])
                ->withCount('messages')
                ->orderByDesc('last_message_at')
                ->paginate(30),
        ]);
    }

    public function show(Request $request, ChatConversation $conversation)
    {
        $request->user()->update(['chat_available' => true, 'last_seen_at' => now()]);
        $conversation->messages()->whereIn('sender_type', ['customer'])->whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.chats.show', [
            'conversation' => $conversation->load(['messages.user', 'user', 'assignedAdmin']),
        ]);
    }

    public function reply(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:3000']]);
        $conversation->update([
            'mode' => 'live',
            'status' => 'open',
            'assigned_admin_id' => $request->user()->id,
            'last_message_at' => now(),
        ]);
        $message = $conversation->messages()->create([
            'user_id' => $request->user()->id,
            'sender_type' => 'admin',
            'message' => $data['message'],
        ]);

        return $request->expectsJson()
            ? response()->json(['id' => $message->id, 'message' => $message->message, 'time' => $message->created_at->format('h:i A')])
            : back();
    }

    public function messages(Request $request, ChatConversation $conversation)
    {
        $request->user()->update(['chat_available' => true, 'last_seen_at' => now()]);
        $messages = $conversation->messages()->where('id', '>', $request->integer('after'))->orderBy('id')->get();

        return response()->json([
            'messages' => $messages->map(fn ($message) => [
                'id' => $message->id,
                'sender' => $message->sender_type,
                'message' => $message->message,
                'time' => $message->created_at->format('M d, h:i A'),
            ]),
        ]);
    }

    public function takeover(Request $request, ChatConversation $conversation)
    {
        $conversation->update([
            'mode' => 'live',
            'assigned_admin_id' => $request->user()->id,
            'status' => 'open',
        ]);

        return back()->with('success', __('Live conversation assigned to you.'));
    }

    public function close(Request $request, ChatConversation $conversation)
    {
        $conversation->update(['status' => 'closed', 'closed_at' => now()]);

        return back()->with('success', __('Conversation closed.'));
    }

    public function presence(Request $request)
    {
        $request->user()->update([
            'chat_available' => $request->boolean('available', true),
            'last_seen_at' => now(),
        ]);

        return response()->json(['available' => $request->user()->chat_available]);
    }
}
