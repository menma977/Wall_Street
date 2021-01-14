@extends('layouts.guest')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <img src="{{ asset('logo.png') }}" alt="Wall Street" class="brand-image img-circle elevation-3" style="opacity: .8; width: 15%">
      <a href="{{ route('welcome') }}"><b>Wall</b><small>Street</small></a>
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg text-xs">
          <b>Reset Password</b>
        </p>
        <form action="{{ route('password.update') }}" method="post">
          @csrf
          <input type="hidden" name="token" value="{{ $request->route('token') }}">
          <div class="input-group mb-3">
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}" autofocus>
            <div class="input-group-append">
              <div class="input-group-text">
                <label for="email" class="fas fa-envelope"></label>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" value="{{ old('password') }}">
            <div class="input-group-append">
              <div class="input-group-text">
                <label for="password" class="fas fa-envelope"></label>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Confirm Password"
                   value="{{ old('password_confirmation') }}">
            <div class="input-group-append">
              <div class="input-group-text">
                <label for="password_confirmation" class="fas fa-envelope"></label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8"></div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('addCss')
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
  <!-- Toastr -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">
@endsection

@section('addJs')
  <!-- SweetAlert2 -->
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <!-- Toastr -->
  <script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>

  <script>
    $(function () {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });

      @if(session('status'))
      Toast.fire({
        icon: 'success',
        title: @json(session('status'))
      })
      @endif

      @if ($errors->any())
      @foreach ($errors->all() as $error)
      Toast.fire({
        icon: 'error',
        title: @json($error)
      })
      @endforeach
      @endif
    });
  </script>
@endsection
