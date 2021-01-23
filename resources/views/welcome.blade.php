@extends('layouts.guest')

@section('content')
  <!-- Page Preloder -->
  <div id="preloder">
    <div class="loader"></div>
  </div>

  <!-- Header section -->
  <header class="header-section clearfix">
    <div class="container-fluid">
      <a href="index.html" class="site-logo">
        <img src="{{ asset('img/logo_small.png')}}" alt="logo">
      </a>
      <div class="responsive-bar"><i class="fa fa-bars"></i></div>
      <nav class="main-menu">
        <ul class="menu-list">
          <li><a href="https://play.google.com/store/apps/details?id=info.wallstreet">Download</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <!-- Header section end -->


  <!-- Hero section -->
  <section class="hero-section">
    <div class="container">
      <div class="row">
        <div class="col-md-6 hero-text">
          <h2>Download App <span>Wall Street</span></h2>
          <h4><span>Now it's even easier to use Wall Street on Google Play and Play Store.</span></h4>
          <div class="row justify-content-center">
            <div class="hero-subscribe-from">
              <a href="{{ url('https://play.google.com/store/apps/details?id=info.wallstreet') }}">
                <button class="site-btn sb-gradients">Via Play Store</button>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <img src="{{ asset('img/laptop.png')}}" class="laptop-image" alt="laptop-wallstreet">
        </div>
      </div>
    </div>
  </section>
  <!-- Hero section end -->

  <!-- Features section -->
  <section class="features-section spad gradient-bg">
    <div class="container text-white">
      <div class="section-title text-center">
        <h2>Our Features</h2>
        <h3>Why should Doge coin?</h3>
      </div>
      <div class="row">
        <!-- feature -->
        <div class="col-md-6 col-lg-4 feature">
          <i class="ti-mobile"></i>
          <div class="feature-content">
            <h4>Famous</h4>
            <p>Doge coin is known throughout the world.</p>
          </div>
        </div>
        <!-- feature -->
        <div class="col-md-6 col-lg-4 feature">
          <i class="ti-wallet"></i>
          <div class="feature-content">
            <h4>Cheap</h4>
            <p>The price is still relatively cheap. </p>
          </div>
        </div>
        <!-- feature -->
        <div class="col-md-6 col-lg-4 feature">
          <i class="ti-money"></i>
          <div class="feature-content">
            <h4>Rising Price</h4>
            <p>The trend in the future is predicted that the price of Doge coin will continue to increase.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Features section end -->

  <!-- About section -->
  <section class="about-section spad">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-6 about-text">
          <h2>About Us</h2>
          <h5>Wall Street community is a decentralized community network based on the DOGECOIN blockchain. and also Wall
            Street:</h5>
          <ul class="list-about">
            <li>Not Ponzi</li>
            <li>Not ROI</li>
            <li>Not an investment scheme.</li>
            <li>Not Trading or Mining.</li>
            <li>No system load / scam.</li>
          </ul>
        </div>
      </div>
      <div class="about-img">
        <img src="img/about-img.png" alt="">
      </div>
    </div>
  </section>
  <!-- About section end -->

  <!-- Footer section -->
  <footer class="footer-section">
    <div class="container">
      <div class="row spad">
        <div class="col-md-6 col-lg-3 footer-widget">
          <img src="{{ asset('img/logo_small.png')}}" class="mb-4" alt="logo">
          <p>WALL STREET community is a decentralized community network based on the DOGECOIN blockchain.</p>
          <span>
          <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
          Copyright &copy;<script>
            document.write(new Date().getFullYear());
          </script> All rights reserved
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></span>
        </div>
        <div class="col-md-6 col-lg-2 offset-lg-1 footer-widget">
          <h5 class="widget-title">HOME</h5>
          <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Download</a></li>
          </ul>
        </div>
        <!-- <div class="col-md-6 col-lg-2 offset-lg-1 footer-widget">
            <h5 class="widget-title">Quick Links</h5>
            <ul>
              <li><a href="#">Network Stats</a></li>
              <li><a href="#">Block Explorers</a></li>
              <li><a href="#">Governance</a></li>
              <li><a href="#">Exchange Markets</a></li>
              <li><a href="#">Get Theme</a></li>
            </ul>
          </div> -->
        <div class="col-md-6 col-lg-3 footer-widget pl-lg-5 pl-3">
          <h5 class="widget-title">Follow Us</h5>
          <div class="social">
            <a href="" class="facebook"><i class="fa fa-facebook"></i></a>
            <a href="" class="google"><i class="fa fa-google-plus"></i></a>
            <a href="" class="instagram"><i class="fa fa-instagram"></i></a>
            <a href="" class="twitter"><i class="fa fa-twitter"></i></a>
          </div>
        </div>
      </div>
    </div>
  </footer>
@endsection

@section('addCss')
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css')}}"/>
  <link rel="stylesheet" href="{{ asset('assets/css/animate.css')}}"/>
  <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.css')}}"/>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}"/>
@endsection

@section('addJs')
  <script src="{{ asset('assets/dist/js/main.js') }}"></script>
  <script src="{{ asset('assets/dist/js/owlcarousel.min.js') }}"></script>
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
@endsection
