@extends('layout')

@section('title', 'Accept Invitation — Sembark URL Shortener')

@section('content')
<div class="page-header">
    <h1>Accept Invitation</h1>
</div>

<p style="margin-bottom: 20px;">You have been invited to join <strong>{{ $invitation->company->name }}</strong> as <strong>{{ $invitation->role->name }}</strong>.</p>

<form method="POST" action="{{ route('invitations.process', $invitation->token) }}">
    @csrf

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name') }}" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <label for="password_confirmation">Confirm Password</label>
    <input type="password" id="password_confirmation" name="password_confirmation" required>

    <div class="form-actions">
        <button type="submit" class="btn">Accept Invitation</button>
    </div>
</form>
@endsection

