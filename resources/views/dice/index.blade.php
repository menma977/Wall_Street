@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Queue</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Queue</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Add/Edit user in dice</h3>
          </div>
          <form method="post" action="{{ route('dice.update') }}">
            @csrf
            <div class="card-body">
              <div class="form-group row">
                <div class="custom-control custom-radio col-md-6">
                  <input class="custom-control-input" type="radio" id="type1" name="type" value="add" {{ old('type') === 'add' ? 'checked' : '' }}>
                  <label for="type1" class="custom-control-label">ADD</label>
                </div>
                <div class="custom-control custom-radio col-md-6">
                  <input class="custom-control-input" type="radio" id="type2" name="type" value="remove" {{ old('type') === 'remove' ? 'checked' : '' }}>
                  <label for="type2" class="custom-control-label">REMOVE</label>
                </div>
              </div>
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" value="{{ old('username') }}">
              </div>
              <div class="form-group">
                <label for="value">Value</label>
                <input type="number" class="form-control" id="value" name="value" placeholder="Enter Value" value="{{ old('value') }}">
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Queue</h3>
            <div class="card-tools">
              <a href="{{ route('dice.index') }}">
                <button type="button" class="btn btn-outline-primary btn-block btn-sm">
                  <i class="fa fa-history"></i>
                  Reload
                </button>
              </a>
            </div>
          </div>
          <div class="card-header">
            @if($dice->hasPages())
              <div class="float-right">
                {{ $dice->links() }}
              </div>
            @endif
            <form method="get" action="{{ route('dice.show') }}">
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
          <div class="card-body p-0 table-responsive">
            <table class="table text-center">
              <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Username</th>
                <th>Total In Dice</th>
              </tr>
              </thead>
              <tbody>
              @foreach($dice as $key => $item)
                <tr>
                  <td>{{ ($dice->currentpage() - 1) * $dice->perpage() + $loop->index + 1 }}.</td>
                  <td>{{ $item->username }}</td>
                  <td>{{ $item->total }}</td>
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