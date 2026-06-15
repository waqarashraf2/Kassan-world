<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function message(Request $request, ChatbotService $bot)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'visitor_token' => ['required', 'uuid'],
            'conversation_id' => ['nullable', 'uuid'],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $conversation = $this->conversation($request, $data);
        $conversation->messages()->create([
            'user_id' => $request->user()?->id,
            'sender_type' => 'customer',
            'message' => $data['message'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        $reply = null;
        if ($conversation->mode !== 'live' || ! $bot->adminOnline()) {
            $result = $bot->answer($data['message'], $request->user());
            $reply = $conversation->messages()->create([
                'sender_type' => 'bot',
                'message' => $result['answer'],
                'matched_faq_id' => $result['faq_id'],
            ]);
            $conversation->update(['last_message_at' => now()]);
        }

        return response()->json([
            'conversation_id' => $conversation->public_id,
            'mode' => $conversation->mode,
            'admin_online' => $bot->adminOnline(),
            'reply' => $reply ? $this->messageData($reply) : null,
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation, ChatbotService $bot)
    {
        $this->authorizeVisitor($request, $conversation);
        $after = max(0, $request->integer('after'));

        return response()->json([
            'messages' => $conversation->messages()->where('id', '>', $after)->orderBy('id')->get()->map($this->messageData(...)),
            'mode' => $conversation->mode,
            'admin_online' => $bot->adminOnline(),
        ]);
    }

    public function live(Request $request, ChatConversation $conversation, ChatbotService $bot)
    {
        $this->authorizeVisitor($request, $conversation);
        $conversation->update(['mode' => 'live', 'status' => 'open', 'last_message_at' => now()]);

        $message = $bot->adminOnline()
            ? 'A KISANWORLD representative has been notified and can now join this conversation.'
            : 'Our representatives are currently away. I will continue helping, and your conversation is waiting for the team.';

        $reply = $conversation->messages()->create(['sender_type' => 'system', 'message' => $message]);

        return response()->json(['reply' => $this->messageData($reply), 'admin_online' => $bot->adminOnline()]);
    }

    private function conversation(Request $request, array $data): ChatConversation
    {
        if (! empty($data['conversation_id'])) {
            $conversation = ChatConversation::where('public_id', $data['conversation_id'])->firstOrFail();
            $this->authorizeVisitor($request, $conversation);

            return $conversation;
        }

        return ChatConversation::create([
            'public_id' => Str::uuid(),
            'visitor_token' => $data['visitor_token'],
            'user_id' => $request->user()?->id,
            'customer_name' => $data['name'] ?? $request->user()?->name,
            'customer_email' => $data['email'] ?? $request->user()?->email,
            'last_message_at' => now(),
        ]);
    }

    private function authorizeVisitor(Request $request, ChatConversation $conversation): void
    {
        abort_unless(hash_equals((string) $conversation->visitor_token, (string) $request->input('visitor_token')), 403);
    }

    private function messageData($message): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender_type,
            'message' => $message->message,
            'time' => $message->created_at?->format('h:i A'),
        ];
    }
}
