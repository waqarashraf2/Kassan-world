<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFaq;
use Illuminate\Http\Request;

class ChatbotFaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = ChatbotFaq::query()
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('question', 'like', '%'.$request->string('q').'%')
                ->orWhere('answer', 'like', '%'.$request->string('q').'%')
                ->orWhere('keywords', 'like', '%'.$request->string('q').'%')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderBy('category')
            ->orderByDesc('priority')
            ->paginate(30)
            ->withQueryString();

        return view('admin.chatbot-faqs.index', [
            'faqs' => $faqs,
            'categories' => ChatbotFaq::distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function update(Request $request, ChatbotFaq $chatbotFaq)
    {
        $chatbotFaq->update($request->validate([
            'answer' => ['required', 'string', 'max:5000'],
            'keywords' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', __('FAQ updated.'));
    }
}
