@extends('layouts.guest')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <img src="{{ asset('logo.png') }}" alt="Wall Street" class="brand-image img-circle elevation-3" style="opacity: .8; width: 20%">
      <a href="{{ route('welcome') }}"><b>Wall</b><small>Street</small></a>
    </div>
  </div>
  <div class="alert alert-success alert-dismissible">
    <h5><i class="icon fas fa-check"></i> Success</h5>
    <b>Your account has been validated</b>
  </div>
@endsection
