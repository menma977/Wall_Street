@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Pool</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('queue.index') }}">Queue</a></li>
        <li class="breadcrumb-item active">Pool</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">List Share</h3>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table text-center">
              <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>MIN</th>
                <th>MAX</th>
                <th>VALUE</th>
                <th>EDIT</th>
              </tr>
              </thead>
              <tbody>
              @foreach($valueList as $key => $item)
                <tr>
                  <td>{{ $loop->index + 1 }}.</td>
                  <td>$ {{ $item->min }}</td>
                  <td>$ {{ $item->max }}</td>
                  <td>{{ $item->value }}</td>
                  <td>
                    <a href="{{ route('queue.pool.edit.limit', $item->id) }}" class="btn btn-block btn-warning btn-xs">EDIT</a>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">BANK</h3>
          </div>
          <form method="post" action="{{ route('queue.pool.update.bank') }}">
            @csrf
            <div class="card-body">
              <div class="form-group">
                <label for="_private_key">Private Key</label>
                <input type="text" class="form-control @error('private_key') is-invalid @enderror" id="_private_key" name="private_key" placeholder="Enter Private Key"
                       value="{{ old('private_key') ? : $bank->private_key }}">
              </div>
              <div class="form-group">
                <label for="_public_key">Public Key</label>
                <input type="text" class="form-control @error('public_key') is-invalid @enderror" id="_public_key" name="public_key" placeholder="Enter Public Key"
                       value="{{ old('public_key') ? : $bank->public_key }}">
              </div>
              <div class="form-group">
                <label for="_wallet_camel">Wallet Camel</label>
                <input type="text" class="form-control @error('wallet_camel') is-invalid @enderror" id="_wallet_camel" name="wallet_camel" placeholder="Enter Wallet Camel"
                       value="{{ old('wallet_camel') ? : $bank->wallet_camel }}">
              </div>
              <div class="form-group">
                <label for="_wallet_tron">Wallet Tron</label>
                <input type="text" class="form-control @error('wallet_tron') is-invalid @enderror" id="_wallet_tron" name="wallet_tron" placeholder="Enter Wallet tron"
                       value="{{ old('wallet_tron') ? : $bank->hex_camel }}">
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-success">Update</button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Queue</h3>
            <div class="card-tools">
              <a href="{{ route('queue.pool.index') }}">
                <button type="button" class="btn btn-outline-primary btn-block btn-sm">
                  <i class="fa fa-history"></i>
                  Reload
                </button>
              </a>
            </div>
          </div>
          <div class="card-header">
            @if($queue->hasPages())
              <div class="float-right">
                {{ $queue->links() }}
              </div>
            @endif
            <form method="get" action="{{ route('queue.pool.show') }}">
              <div class="input-group">
                <input type="text" name="search" class="form-control float-right" placeholder="Search">

                <div class="input-group-append">
                  <button type="submit" class="btn btn-default">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
          <div class="card-header">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <a href="{{ route('queue.pool.update.setting', 1) }}" class="btn btn-outline-success {{ $queueDailySetting->is_on ? 'active' : '' }}">
                <input type="radio" name="options" id="option_a1" autocomplete="off" {{ $queueDailySetting->is_on ? 'checked' : '' }}> ON
              </a>
              <a href="{{ route('queue.pool.update.setting', 0) }}" class="btn btn-outline-danger {{ $queueDailySetting->is_on ? '' : 'active' }}">
                <input type="radio" name="options" id="option_a3" autocomplete="off" {{ $queueDailySetting->is_on ? '' : 'checked' }}> OFF
              </a>
            </div>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table text-center">
              <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th style="width: 10px">Status</th>
                <th>User</th>
                <th>Total Package</th>
                <th>Share in USD (DYNAMIC)</th>
                <th>Share in Camel</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
              @foreach($queue as $key => $item)
                <tr>
                  <td>{{ ($queue->currentpage() - 1) * $queue->perpage() + $loop->index + 1 }}.</td>
                  <td>
                    @if($item->status)
                      <span class="badge bg-success">DONE</span>
                    @else
                      <span class="badge bg-warning">WAITING</span>
                    @endif
                  </td>
                  <td>{{ $item->user->username }}</td>
                  <td>$ {{ $item->totalUpgrade }}</td>
                  <td>$ {{ $item->shareUsd }}</td>
                  <td>{{ $item->shareValue }}</td>
                  <td>{{ $item->date }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection