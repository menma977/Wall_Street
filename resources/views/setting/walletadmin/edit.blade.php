@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>Wallet Admin</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Settings
      </li>
      <li class="breadcrumb-item active">
        Wallet Admin
      </li>
      <li class="breadcrumb-item active">
        Edit
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <div class="card-title">Update {{ $wallet->name }}</div>
  </div>
  <form
    action="{{ route("setting.wallet-admin.update",["id"=>Illuminate\Support\Facades\Crypt::encrypt($wallet->id)]) }}"
    method="post">
    @csrf
    <input type="hidden" name="id" value="{{ $wallet->id }}">
    <div class="card-body">
      <div class="form-group">
        <label for="wallet-name">Name :</label>
        <input type="text" class="form-control" id="wallet-name" name="name" required placeholder=""
          value="{{ old("name") ?: $wallet->name }}" />
      </div>
      <div class="form-group">
        <label for="wallet-camel">Wallet Camel :</label>
        <input type="text" class="form-control" id="wallet-camel" name="camel" required placeholder=""
          value="{{ old("camel") ?: $wallet->wallet_camel }}" />
      </div>
      <div class="form-group">
        <label for="wallet-btc">Wallet Bitcoin</label>
        <input type="text" class="form-control" id="wallet-btc" name="btc" required placeholder=""
          value="{{ old("btc") ?: $wallet->wallet_btc }}" />
      </div>
      <div class="form-group">
        <label for="wallet-ltc">Wallet Litecoin</label>
        <input type="text" class="form-control" id="wallet-ltc" name="ltc" required placeholder=""
          value="{{ old("ltc") ?: $wallet->wallet_ltc }}" />
      </div>
      <div class="form-group">
        <label for="wallet-eth">Wallet Etherum</label>
        <input type="text" class="form-control" id="wallet-eth" name="eth" required placeholder=""
          value="{{ old("eth") ?: $wallet->wallet_eth }}" />
      </div>
      <div class="form-group">
        <label for="wallet-doge">Wallet Dogecoin</label>
        <input type="text" class="form-control" id="wallet-doge" name="doge" required placeholder=""
          value="{{ old("doge") ?: $wallet->wallet_doge }}" />
      </div>
    </div>
    <div class="card-footer">
      <button class="btn btn-warning">
        <i class="fas fa-edit"></i> Update Wallet
      </button>
    </div>
  </form>
</div>
@endsection
