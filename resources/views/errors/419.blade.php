@extends('layouts.app')

@section('title', 'Session Expired')

@section('content')
<div class="container py-5 text-center">
    <h1>Session Expired</h1>
    <p>Your session has expired or the form token is invalid. Please refresh the page and try again.</p>
    <a href="{{ url()->current() }}" class="btn btn-primary mt-3">Reload</a>
</div>
@endsection
