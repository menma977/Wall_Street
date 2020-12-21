@extends('layouts.guest')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <img src="{{ asset('logo.png') }}" alt="Wall Street" class="brand-image img-circle elevation-3" style="opacity: .8; width: 100%">
      <a href="{{ route('welcome') }}"><b>Wall</b><small>Street</small></a>
    </div>
  </div>
@endsection
