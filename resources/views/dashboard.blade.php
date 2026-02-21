@extends('layout')

@section('title', 'Dashboard — Sembark URL Shortener')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
</div>

<p>Welcome, <strong>{{ $user->name }}</strong>.</p>
<p>Email: {{ $user->email }}</p>
<p>Company: {{ $user->company ? $user->company->name : 'N/A' }}</p>
@php
    $roles = $user->roles()->pluck('name')->toArray();
@endphp
<p>Roles: {{ implode(', ', $roles) }}</p>
@endsection

