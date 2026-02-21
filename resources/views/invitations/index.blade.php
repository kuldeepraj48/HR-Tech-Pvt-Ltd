@extends('layout')

@section('title', 'Invitations — Sembark URL Shortener')

@section('content')
<div class="page-header">
    <h1>Invitations</h1>
    <div class="header-actions">
        <a href="{{ route('invitations.create') }}" class="btn">Create Invitation</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Email</th>
            <th>Company</th>
            <th>Role</th>
            <th>Invited By</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invitations as $invitation)
            <tr>
                <td>{{ $invitation->email }}</td>
                <td>{{ $invitation->company->name }}</td>
                <td>{{ $invitation->role->name }}</td>
                <td>{{ $invitation->inviter->name }}</td>
                <td>{{ $invitation->isAccepted() ? 'Accepted' : 'Pending' }}</td>
                <td>{{ $invitation->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No invitations found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection

