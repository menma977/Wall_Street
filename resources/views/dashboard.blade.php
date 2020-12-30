@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Dashboard</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">Dashboard</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        <div class="info-box bg-primary">
          <span class="info-box-icon"><i class="fas fa-spinner"></i></span>
          <div class="info-box-content">
            <b class="info-box-text">Queue</b>
            <span id="queueTarget" class="info-box-number">0</span>
            <div class="progress">
              <div id="queueProgress" class="progress-bar" style="width: 0"></div>
            </div>
            <div id="queueDescription" class="progress-description"></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-primary">
          <span class="info-box-icon"><i class="far fa-sun"></i></span>
          <div class="info-box-content">
            <b class="info-box-text">Share Queue</b>
            <span id="shareTarget" class="info-box-number">0</span>
            <div class="progress">
              <div id="shareProgress" class="progress-bar" style="width: 0"></div>
            </div>
            <div id="shareDescription" class="progress-description"></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-cyan">
          <span class="info-box-icon"><i class="fas fa-sync"></i></span>
          <div class="info-box-content">
            <b class="info-box-text">Not Verified User</b>
            <span class="info-box-number">{{ $verifiedUser }}</span>
            <div class="progress">
              <div class="progress-bar" style="width: {{ $verifiedProgress }}%"></div>
            </div>
            <div class="progress-description">
              {{ $verifiedProgress }}% has Verified
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-orange">
          <span class="info-box-icon"><i class="fab fa-ethereum"></i></span>
          <div class="info-box-content">
            <b class="info-box-text">Total Upgrade</b>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <h4 class="info-box-number">{{ $verifiedUser }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-teal">
          <span class="info-box-icon"><i class="far fa-chart-bar"></i></span>
          <div class="info-box-content">
            <b class="info-box-text">Total Transaction Camel</b>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <h4 class="info-box-number">{{ $countHistoryCamel }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-info">
          <span class="info-box-icon"><i class="far fa-chart-bar"></i></span>
          <div class="info-box-content">
            <b class="info-box-text">Total Level</b>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <h4 class="info-box-number">{{ $totalUser }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('addJs')
  <script>
    $(function () {
      getQueue();
      getShareQueue();

      setInterval(function () {
        getQueue();
        getShareQueue();
      }, 1000);
    });

    function getQueue() {
      fetch("{{ route('dashboard.queue') }}", {
        method: 'get',
        headers: new Headers({
          'Content-Type': 'application/x-www-form-urlencoded',
          "X-CSRF-TOKEN": $("input[name='_token']").val()
        }),
      }).then((response) => response.json()).then((result) => {
        $('#queueTarget').html(result.target);
        $('#queueProgress').css('width', result.progress + '%');
        $('#queueDescription').html(result.progress + '% waiting to send');
      }).catch((error) => {
        $('#queueDescription').html(error);
      });
    }

    function getShareQueue() {
      fetch("{{ route('dashboard.queue.share') }}", {
        method: 'get',
        headers: new Headers({
          'Content-Type': 'application/x-www-form-urlencoded',
          "X-CSRF-TOKEN": $("input[name='_token']").val()
        }),
      }).then((response) => response.json()).then((result) => {
        $('#shareTarget').html(result.target);
        $('#shareProgress').css('width', result.progress + '%');
        $('#shareDescription').html(result.progress + '% waiting to send');
      }).catch((error) => {
        $('#shareDescription').html(error);
      });
    }
  </script>
@endsection