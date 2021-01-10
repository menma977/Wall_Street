<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>WALL Street</title>
  <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon">
  <meta content='{{ asset('logo.png') }}' property='og:image'/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon icon -->
  <link rel="shortcut icon" href="{{ asset('assets/dist/img/logo.jpg') }}" type="image/x-icon">
  <!-- Theme Style -->
  <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ url('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="{{ url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700') }}" rel="stylesheet">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
  <!-- Toastr -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">
  @yield('addCss')
</head>

<body class="sidebar-mini accent-primary">
<div id="app" class="wrapper">
  <!-- header -->
  <x-header/>
  <!-- /.header -->

  <!-- sidebar -->
  <x-side-bar/>
  <!-- /sidebar -->

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        @yield('title')
      </div>
    </section>

    <section class="content">
      @yield('content')
    </section>
  </div>

  <footer class="main-footer text-sm">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 0.0.1 BETA
    </div>
    <strong>Copyright &copy; 2020 <a href="#">SEO Catalog</a>.</strong> All rights reserved.
  </footer>


  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
  </form>
</div>
<!-- jQuery -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
@yield('addJs')

<script>
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 6000
  });
  $(function () {

    @if(session()->has('message'))
    Toast.fire({
      icon: 'success',
      title: @json(session()->get('message'))
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
</body>

</html>
