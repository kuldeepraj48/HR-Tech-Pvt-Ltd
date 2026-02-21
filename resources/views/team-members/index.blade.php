@extends('layout')

@section('title', 'Team Members — Client Admin')

@section('content')
<div class="page-header">
    <h1>Client Admin Dashboard</h1>
    <div class="header-actions">
        <a href="{{ route('invitations.create') }}" class="btn">Invite</a>
    </div>
</div>

<h2 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600;">Team Members</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Total Generated URLs</th>
            <th>Total URL Hits</th>
        </tr>
    </thead>
    <tbody>
        @forelse($teamMembers as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>{{ $member->roles->first()->name ?? 'N/A' }}</td>
                <td>{{ $member->short_urls_count }}</td>
                <td>{{ $member->total_hits ?? 0 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No team members found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination-wrap">
    <div class="pagination-info">Showing {{ count($teamMembers) }} of {{ count($teamMembers) }} team members</div>
</div>
@endsection




