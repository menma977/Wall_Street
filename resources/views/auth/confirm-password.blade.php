@extends('layouts.guest')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <img src="{{ asset('logo.png') }}" alt="Wall Street" class="brand-image img-circle elevation-3" style="opacity: .8; width: 15%">
      <a href="{{ route('welcome') }}"><b>Wall</b><small>Street</small></a>
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">This is a secure area of the application.</p>
        <p class="login-box-msg">Please confirm your password before continuing.</p>

        <form action="{{ route('password.confirm') }}" method="post">
          @csrf
          <div class="input-group">
            <input id="_password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <label for="_password" class="fas fa-lock"></label>
              </div>
            </div>
          </div>
          <div class="mb-3">
            @error('password')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="row">
            <div class="col-8"></div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Confirm</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
