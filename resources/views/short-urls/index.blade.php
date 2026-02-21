@extends('layout')

@section('title', 'Generated Short URLs — Sembark URL Shortener')

@section('content')
@php
    $user = Auth::user();
    $isAdmin = $user->hasRole('Admin');
    $isMember = $user->hasRole('Member');
    $canCreate = $user->hasRole('Sales') || $user->hasRole('Manager');
@endphp

<div class="page-header">
    <h1>
        @if($isAdmin)
            Client Admin Dashboard
        @elseif($isMember)
            Client Member Dashboard
        @else
            Sembark URL Shortener — Dashboard
        @endif
    </h1>
    <div class="header-actions">
        @if($canCreate)
            <a href="{{ route('short-urls.create') }}" class="btn">Generate</a>
        @endif
    </div>
</div>

<h2 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600;">Generated Short URLs</h2>

<div class="table-actions">
    <div class="left">
        <form method="GET" action="{{ route('short-urls.index') }}" style="display: inline-block; margin: 0; max-width: none;">
            <label for="date_filter" style="display: inline; margin-right: 8px;">View by date:</label>
            <select name="date_filter" id="date_filter" onchange="this.form.submit()" style="width: auto; display: inline-block;">
                <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="last_week" {{ $dateFilter == 'last_week' ? 'selected' : '' }}>Last Week</option>
                <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
            </select>
        </form>
    </div>
    <div class="right">
        <a href="{{ route('short-urls.download', ['date_filter' => $dateFilter]) }}" class="btn">Download</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Short URL</th>
            <th>Long URL</th>
            <th>Hits</th>
            @if($isAdmin)
                <th>User</th>
            @endif
            <th>Created On</th>
        </tr>
    </thead>
    <tbody>
        @forelse($shortUrls as $shortUrl)
            <tr>
                <td><a href="{{ route('short-urls.redirect', $shortUrl->short_code) }}" target="_blank">{{ url('/s/' . $shortUrl->short_code) }}</a></td>
                <td>{{ \Illuminate\Support\Str::limit($shortUrl->original_url, 50) }}</td>
                <td>{{ $shortUrl->hits }}</td>
                @if($isAdmin)
                    <td>{{ $shortUrl->user->name }}</td>
                @endif
                <td>{{ $shortUrl->created_at->format('d M \'y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $isAdmin ? 5 : 4 }}">No short URLs found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination-wrap">
    <div class="pagination-info">Showing {{ $shortUrls->firstItem() ?? 0 }} to {{ $shortUrls->lastItem() ?? 0 }} of {{ $shortUrls->total() }}</div>
    @if($shortUrls->hasPages())
        <div class="pagination-links">
            @if($shortUrls->previousPageUrl())
                <a href="{{ $shortUrls->previousPageUrl() }}" class="btn">Prev</a>
            @endif
            @if($shortUrls->nextPageUrl())
                <a href="{{ $shortUrls->nextPageUrl() }}" class="btn">Next</a>
            @endif
            @if($isAdmin && $shortUrls->hasMorePages())
                <a href="{{ route('short-urls.index', ['date_filter' => $dateFilter, 'page' => 'all']) }}" class="btn">View All</a>
            @endif
        </div>
    @endif
</div>
@endsection
