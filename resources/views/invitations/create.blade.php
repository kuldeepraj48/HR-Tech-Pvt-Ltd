@extends('layout')

@section('title', 'Invite New Team Member — Client Admin')

@section('content')
<div class="page-header">
    <h1>Client Admin Dashboard</h1>
</div>

<h2 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600;">Invite New Team Member</h2>

<form method="POST" action="{{ route('invitations.store') }}">
    @csrf

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Full name">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. sample@example.com" required>

    <label for="role_id">Role</label>
    <select id="role_id" name="role_id" required>
        <option value="">Select Role</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
        @endforeach
    </select>

    <div class="form-actions">
        <button type="submit" class="btn">Send Invitation</button>
        <a href="{{ route('team-members.index') }}" class="btn" style="background: #6c757d;">Cancel</a>
    </div>
</form>
@endsection

