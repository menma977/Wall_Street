@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Detail</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
        <li class="breadcrumb-item active">Detail</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <button id="load_balance" type="button" class="btn btn-primary btn-block elevation-1 shadow mb-2">
      <i class="fas fa-sync"></i>
      Load Balance
    </button>
    <div class="row">
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="fab fa-btc"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">BTC</span>
            <span id="btc" class="info-box-number">0</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="fab fa-ethereum"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">ETH</span>
            <span id="eth" class="info-box-number">0</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="fas fa-money-check-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">LTC</span>
            <span id="ltc" class="info-box-number">0</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="far fa-money-bill-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">DOGE</span>
            <span id="doge" class="info-box-number">0</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="	fas fa-money-bill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">CAMEL</span>
            <span id="camel" class="info-box-number">0</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="	fas fa-money-bill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">TRON</span>
            <span id="tron" class="info-box-number">0</span>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="fab fa-btc"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">BTC Wall</span>
            <span class="info-box-number">{{ $btcBalance }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="fab fa-ethereum"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">ETH Wall</span>
            <span class="info-box-number">{{ $ethBalance }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="fas fa-money-check-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">LTC Wall</span>
            <span class="info-box-number">{{ $ltcBalance }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="far fa-money-bill-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">DOGE Wall</span>
            <span class="info-box-number">{{ $dogeBalance }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box shadow bg-primary">
          <span class="info-box-icon bg-primary"><i class="	fas fa-money-bill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">CAMEL Wall</span>
            <span class="info-box-number">{{ $camelBalance }}</span>
          </div>
        </div>
      </div>
    </div>
    <form action="{{ route("users.update", $user->id) }}" method="post">
      @csrf
      <div class="card card-outline card-primary collapsed-card">
        <div class="card-header">
          <h3 class="card-title">Edit Profile</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="card-body" style="display: none;">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="_username">Username</label>
                <input type="text" class="form-control" id="_username" name="username" placeholder="Enter username" value="{{ old("username") ?? $user->username }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="_email">Email address</label>
                <input type="email" class="form-control" id="_email" name="email" placeholder="Enter email" value="{{ old("email") ?? $user->email }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="_password">Password</label>
                <input type="text" class="form-control" id="_password" name="password" placeholder="Enter password" value="{{ old("password") ?? $user->password_junk }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="_secondary_password">Secondary password</label>
                <input type="text" class="form-control" id="_secondary_password" name="secondary_password" placeholder="Enter secondary password"
                       value="{{ old("secondary_password") ?? $user->secondary_password_junk }}">
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning">Update</button>
        </div>
      </div>
    </form>
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Profile</h3>
      </div>
      <div class="card-body">
        <h3 class="profile-username text-center">{{ $user->name }}</h3>
        <p class="text-muted text-center">{{ $user->username }}</p>
        <p class="text-muted text-center">{{ $user->email }}</p>

        <div class="row">
          <div class="col-md-6">
            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Wallet Camel</b> <small class="float-right">{{ $user->wallet_camel }}</small>
              </li>
              <li class="list-group-item">
                <b>Wallet Doge</b> <small class="float-right">{{ $user->wallet_doge }}</small>
              </li>
              <li class="list-group-item">
                <b>Wallet BTC</b> <small class="float-right">{{ $user->wallet_btc }}</small>
              </li>
              <li class="list-group-item">
                <b>Wallet ETH</b> <small class="float-right">{{ $user->wallet_eth }}</small>
              </li>
              <li class="list-group-item">
                <b>Wallet LTC</b> <small class="float-right">{{ $user->wallet_ltc }}</small>
              </li>
            </ul>
          </div>
          <div class="col-md-6">
            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Password</b> <small class="float-right">{{ $user->password_junk }}</small>
              </li>
              <li class="list-group-item">
                <b>Secondary Password</b> <small class="float-right">{{ $user->secondary_password_junk }}</small>
              </li>
              <li class="list-group-item">
                <b>Username Coin</b> <small class="float-right">{{ $user->username_doge }}</small>
              </li>
              <li class="list-group-item">
                <b>Password Coin</b> <small class="float-right">{{ $user->password_doge }}</small>
              </li>
              <li class="list-group-item">
                <div class="progress">
                  <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $progress }}%">
                    <span>$ {{ $credit }}</span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <a href="{{ route('users.suspend', $user->id) }}">
          @if($user->suspend)
            <button type="button" class="btn btn-success btn-block elevation-1 shadow mb-2">
              Unsuspend
            </button>
          @else
            <button type="button" class="btn btn-warning btn-block elevation-1 shadow mb-2">
              Suspend
            </button>
          @endif
        </a>
      </div>
      <div class="col-md-6">
        <a href="{{ route('setting.delete.dice', $user->id) }}">
          <button type="button" class="btn btn-danger btn-block elevation-1 shadow mb-2">
            <i class="fas fa-dice-five"></i>
            Delete DICE
          </button>
        </a>
      </div>
    </div>
  </div>
@endsection

@section('addJs')
  <script>
    $(function () {
      $('#load_balance').on('click', function () {
        fetch("{{ route('users.balance', $user->id) }}", {
          method: 'GET',
          headers: new Headers({
            'Content-Type': 'application/x-www-form-urlencoded',
            "X-CSRF-TOKEN": $("input[name='_token']").val(),
            "Access-Control-Allow-Origin": "*",
          }),
        }).then((response) => response.json()).then((response) => {
          console.log(response);
          $("#camel").html(response.camel);
          $("#tron").html(response.tron);
          $("#btc").html(response.btc);
          $("#doge").html(response.doge);
          $("#ltc").html(response.ltc);
          $("#eth").html(response.eth);
        }).catch((error) => {
          console.log(error);
        });
      });
    });
  </script>
@endsection