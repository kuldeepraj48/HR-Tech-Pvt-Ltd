@extends('layout')

@section('title', 'Clients — Super Admin')

@section('content')
<div class="page-header">
    <h1>Super Admin Dashboard</h1>
    <div class="header-actions">
        <a href="{{ route('invitations.create') }}" class="btn">Invite</a>
    </div>
</div>

<h2 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600;">Clients</h2>

<table>
    <thead>
        <tr>
            <th>Client Name</th>
            <th>Total Members</th>
            <th>Total Shortened URLs</th>
            <th>Total Clicks</th>
        </tr>
    </thead>
    <tbody>
        @forelse($companies as $company)
            <tr>
                <td>{{ $company->name }}</td>
                <td>{{ $company->users_count }}</td>
                <td>{{ $company->short_urls_count }}</td>
                <td>{{ $company->short_urls_sum_hits ?? 0 }}</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left: 20px; color: #666;">{{ $company->users->first()->email ?? 'No users yet' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No clients found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination-wrap">
    <div class="pagination-info">Showing {{ $companies->firstItem() ?? 0 }} to {{ $companies->lastItem() ?? 0 }} of {{ $companies->total() }}</div>
    @if($companies->hasPages())
        <div class="pagination-links">
            @if($companies->previousPageUrl())
                <a href="{{ $companies->previousPageUrl() }}" class="btn">Prev</a>
            @endif
            @if($companies->nextPageUrl())
                <a href="{{ $companies->nextPageUrl() }}" class="btn">Next</a>
            @endif
            @if($companies->hasMorePages())
                <a href="{{ route('clients.index', ['page' => 'all']) }}" class="btn">View All</a>
            @endif
        </div>
    @endif
</div>
@endsection




