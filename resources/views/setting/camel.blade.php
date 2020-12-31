@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>Camel Settings</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Settings
      </li>
      <li class="breadcrumb-item active">
        Camel
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <form method="POST" action="{{ route("setting.camel.edit") }}">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label>Private Key</label>
            <input type="text" class="form-control" name="privateKey" value="{{ $camelSetting->private_key }}" />
          </div>
          <div class="form-group">
            <label>Public Key</label>
            <input type="text" class="form-control" name="publicKey" value="{{ $camelSetting->public_key }}" />
          </div>
          <div class="form-group">
            <label>Wallet Camel</label>
            <input type="text" class="form-control" name="walletCamel" value="{{ $camelSetting->wallet_camel }}" />
          </div>
          <div class="form-group">
            <label>Hex Camel</label>
            <input type="text" class="form-control" name="hexCamel" value="{{ $camelSetting->hex_camel }}" />
          </div>
          <div class="form-group">
            <label>To Dollar</label>
            <input type="number" class="form-control" name="toDollar" value="{{ $camelSetting->to_dollar }}" />
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-success" type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
