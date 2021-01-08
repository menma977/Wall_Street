@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>BANK Settings</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">
          Settings
        </li>
        <li class="breadcrumb-item active">
          BANK
        </li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card bg-primary elevation-1">
      <div class="card-header">
        <h3 class="card-title">All Balance - <small id="description_balance">please click button refresh to load balance</small></h3>
        <div class="card-tools">
          <button id="load_balance" type="button" class="btn btn-tool" data-card-widget="card-refresh" data-source="{{ Request::url() }}" data-source-selector="#card-refresh-content">
            <i class="fas fa-sync-alt"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="info-box elevation-2 bg-primary">
              <span class="info-box-icon bg-primary"><i class="fab fa-btc"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">BTC</span>
                <span id="btc" class="info-box-number">0</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-box elevation-2 bg-primary">
              <span class="info-box-icon bg-primary"><i class="fab fa-ethereum"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">ETH</span>
                <span id="eth" class="info-box-number">0</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-box elevation-2 bg-primary">
              <span class="info-box-icon bg-primary"><i class="fas fa-money-check-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">LTC</span>
                <span id="ltc" class="info-box-number">0</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-box elevation-2 bg-primary">
              <span class="info-box-icon bg-primary"><i class="far fa-money-bill-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">DOGE</span>
                <span id="doge" class="info-box-number">0</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-box elevation-2 bg-primary">
              <span class="info-box-icon bg-primary"><i class="	fas fa-money-bill"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">CAMEL</span>
                <span id="camel" class="info-box-number">0</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-box elevation-2 bg-primary">
              <span class="info-box-icon bg-primary"><i class="	fas fa-money-bill"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">TRON</span>
                <span id="tron" class="info-box-number">0</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Withdraw Camel/TRON</h3>
      </div>
      <form method="POST" action="{{ route('setting.camel.store') }}">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <div class="custom-control custom-radio">
              <input class="custom-control-input" type="radio" id="type_wallet1" name="type_wallet" value="camel" checked>
              <label for="type_wallet1" class="custom-control-label">Camel</label>
            </div>
            <div class="custom-control custom-radio">
              <input class="custom-control-input" type="radio" id="type_wallet2" name="type_wallet" value="tron">
              <label for="type_wallet2" class="custom-control-label">Tron</label>
            </div>
          </div>
          <div class="form-group">
            <label>Wallet</label>
            <input type="text" class="form-control @error("wallet") is-invalid @enderror" name="wallet" value="{{ old('wallet') }}"/>
          </div>
          <div class="form-group">
            <label>Amount</label>
            <input type="text" class="form-control @error("amount") is-invalid @enderror" name="amount" value="{{ old('amount') }}"/>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" type="submit">Submit</button>
        </div>
      </form>
    </div>

    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Edit BANK CAMEL</h3>
      </div>
      <form method="POST" action="{{ route("setting.camel.edit") }}">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Private Key</label>
                <input type="text" class="form-control @error("privateKey") is-invalid @enderror" name="privateKey" value="{{ old('privateKey') ?: $camelSetting->private_key }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Public Key</label>
                <input type="text" class="form-control @error("publicKey") is-invalid @enderror" name="publicKey" value="{{ old('publicKey') ?: $camelSetting->public_key }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Wallet Camel</label>
                <input type="text" class="form-control @error("walletCamel") is-invalid @enderror" name="walletCamel" value="{{ old('walletCamel') ?: $camelSetting->wallet_camel }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Hex Camel</label>
                <input type="text" class="form-control @error("hexCamel") is-invalid @enderror" name="hexCamel" value="{{ old('hexCamel') ?: $camelSetting->hex_camel }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Value To Share</label>
                <input type="text" class="form-control @error("value") is-invalid @enderror" name="share_value" value="{{ old('value') ?: $camelSetting->share_value }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Share Time</label>
                <select class="form-control @error("hexCamel") is-invalid @enderror" name="share_time">
                  @for($i = 1; $i <= 120; $i++)
                    <option value="{{ $i }}" {{ $i == $camelSetting->share_time ? 'selected' : '' }}>{{ $i }} Minute</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" type="submit">Submit</button>
        </div>
      </form>
    </div>

    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Edit BANK COIN</h3>
      </div>
      <form method="POST" action="{{ route("setting.bank.edit") }}">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control @error("username") is-invalid @enderror" name="username" value="{{ old('username') ?: $bankSetting->username }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Password</label>
                <input type="text" class="form-control @error("password") is-invalid @enderror" name="password" value="{{ old('password') ?: $bankSetting->password }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Wallet BTC</label>
                <input type="text" class="form-control @error("btc") is-invalid @enderror" name="btc" value="{{ old('btc') ?: $bankSetting->wallet_btc }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Wallet LTC</label>
                <input type="text" class="form-control @error("ltc") is-invalid @enderror" name="ltc" value="{{ old('ltc') ?: $bankSetting->wallet_ltc }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Wallet ETH</label>
                <input type="text" class="form-control @error("eth") is-invalid @enderror" name="eth" value="{{ old('eth') ?: $bankSetting->wallet_eth }}"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Wallet DOGE</label>
                <input type="text" class="form-control @error("doge") is-invalid @enderror" name="doge" value="{{ old('doge') ?: $bankSetting->wallet_doge }}"/>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('addJs')
  <script>
    $(function () {
      $('#load_balance').on('click', function () {
        fetch("{{ route('setting.balance') }}", {
          method: 'GET',
          headers: new Headers({
            'Content-Type': 'application/x-www-form-urlencoded',
            "X-CSRF-TOKEN": $("input[name='_token']").val(),
            "Access-Control-Allow-Origin": "*",
          }),
        }).then((response) => response.json()).then((response) => {
          $("#description_balance").html(response.message);
          $("#camel").html(response.camel);
          $("#tron").html(response.tron);
          $("#btc").html(response.btc);
          $("#doge").html(response.doge);
          $("#ltc").html(response.ltc);
          $("#eth").html(response.eth);
        }).catch((error) => {
          $("#description_balance").html(error);
        });
      });
    });
  </script>
@endsection