@extends('layouts.admin')
@section('title', 'Blogs')
@section('heading', 'Blogs')
@section('content')
<div class="admin-page-heading"><div><h1>Blog articles</h1><p>Manage Urdu and English agriculture content.</p></div><a class="admin-primary" href="{{ route('admin.blogs.create') }}">Add Article</a></div>
<section class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Article</th><th>Language</th><th>Category</th><th>Status</th><th>Published</th><th></th></tr></thead><tbody>
@forelse($blogs as $blog)<tr><td><strong>{{ $blog->title }}</strong><small>{{ $blog->author?->name }}</small></td><td>{{ strtoupper($blog->language) }}</td><td>{{ $blog->category?->name }}</td><td><span class="admin-status {{ $blog->isPubliclyVisible() ? 'completed' : 'pending' }}">{{ $blog->isPubliclyVisible() ? 'Published' : ($blog->status === 'published' ? 'Scheduled' : ucfirst($blog->status)) }}</span></td><td>{{ $blog->published_at?->format('M d, Y H:i') ?? ($blog->status === 'published' ? 'Immediately' : 'Not scheduled') }}</td><td class="admin-actions">@if($blog->isPubliclyVisible())<a href="{{ route($blog->language==='ur' ? 'blogs.urdu.show' : 'blogs.english.show',$blog->slug) }}" target="_blank">View</a>@endif<a href="{{ route('admin.blogs.edit',$blog) }}">Edit</a><x-admin.delete-form :action="route('admin.blogs.destroy',$blog)" /></td></tr>
@empty<tr><td colspan="6" class="admin-empty">No articles found.</td></tr>@endforelse
</tbody></table></div><div class="admin-pagination">{{ $blogs->links() }}</div></section>
@endsection
