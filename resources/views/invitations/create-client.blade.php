@extends('layout')

@section('title', 'Invite New Client — Super Admin')

@section('content')
<div class="page-header">
    <h1>Super Admin Dashboard</h1>
</div>

<h2 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600;">Invite New Client</h2>

<form method="POST" action="{{ route('invitations.store') }}">
    @csrf

    <label for="name">Client Name</label>
    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Company or client name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. admin@client.com" required>

    <div class="form-actions">
        <button type="submit" class="btn">Send Invitation</button>
        <a href="{{ route('clients.index') }}" class="btn" style="background: #6c757d;">Cancel</a>
    </div>
</form>
@endsection




