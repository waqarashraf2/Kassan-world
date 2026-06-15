@extends('layouts.admin')
@section('title', 'Chatbot FAQs')
@section('heading', 'Chatbot FAQs')
@section('content')
<div class="admin-page-heading"><div><h1>FAQ knowledge base</h1><p>{{ number_format($faqs->total()) }} searchable customer questions.</p></div></div>
<form class="admin-card admin-faq-filter" method="GET"><input name="q" value="{{ request('q') }}" placeholder="Search question, answer or keywords"><select name="category"><option value="">All categories</option>@foreach($categories as $category)<option @selected(request('category')===$category)>{{ $category }}</option>@endforeach</select><button class="admin-primary">Search</button></form>
<div class="admin-faq-list">@foreach($faqs as $faq)<details class="admin-form-card"><summary><div><span>{{ $faq->category }}</span><strong>{{ $faq->question }}</strong></div><b>{{ $faq->is_active ? 'Active' : 'Disabled' }}</b></summary><form action="{{ route('admin.chatbot-faqs.update', $faq) }}" method="POST">@csrf @method('PUT')<label>Answer<textarea name="answer" rows="5" required>{{ $faq->answer }}</textarea></label><label>Search keywords<textarea name="keywords" rows="2">{{ $faq->keywords }}</textarea></label><label>Priority<input type="number" name="priority" min="0" max="1000" value="{{ $faq->priority }}"></label><label class="admin-check-field"><input type="checkbox" name="is_active" value="1" @checked($faq->is_active)> Active</label><button class="admin-primary">Save FAQ</button></form></details>@endforeach</div>
<div class="admin-pagination">{{ $faqs->links() }}</div>
@endsection
