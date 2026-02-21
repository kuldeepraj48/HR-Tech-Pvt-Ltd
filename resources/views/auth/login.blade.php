@extends('layout')

@section('title', 'Login — Sembark URL Shortener')

@section('content')
<div class="page-header">
    <h1>Sembark URL Shortener</h1>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. sergiu@example.com" required autofocus>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="********" required>

    <label style="margin-top: 12px;">
        <input type="checkbox" name="remember"> Remember me
    </label>

    <div class="form-actions">
        <button type="submit" class="btn">Login</button>
    </div>
</form>
@endsection

