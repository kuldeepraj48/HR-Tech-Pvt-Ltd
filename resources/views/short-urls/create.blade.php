@extends('layout')

@section('title', 'Generate Short URL — Sembark URL Shortener')

@section('content')
@php
    $user = Auth::user();
    $isAdmin = $user->hasRole('Admin');
@endphp

<div class="page-header">
    <h1>@if($isAdmin) Client Admin Dashboard @else Sembark URL Shortener — Generate URL @endif</h1>
</div>

<h2 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600;">Generate Short URL</h2>

<form method="POST" action="{{ route('short-urls.store') }}">
    @csrf

    <label for="original_url">Original URL</label>
    <input type="url" id="original_url" name="original_url" value="{{ old('original_url') }}" placeholder="e.g. https://sembark.com/client-member/dashboard/edit-url?id=45&type=edit" required>

    <div class="form-actions">
        <button type="submit" class="btn">Generate</button>
    </div>
</form>
@endsection
