@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Share</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('queue.index') }}">Queue</a></li>
        <li class="breadcrumb-item active">Share</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Queue</h3>
        <div class="card-tools">
          <a href="{{ route('queue.share.index') }}">
            <button type="button" class="btn btn-outline-primary btn-block btn-sm">
              <i class="fa fa-history"></i>
              Reload
            </button>
          </a>
        </div>
      </div>
      <div class="card-header">
        @if($queue->hasPages())
          <div class="float-right table-responsive">
            {{ $queue->links() }}
          </div>
        @endif
        <form method="get" action="{{ route('queue.share.show', 'search') }}">
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
            <th style="width: 10px">Status</th>
            <th>User</th>
            <th>Type</th>
            <th>Package</th>
            <th>Send Date</th>
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
              <td>{{ $item->type }}</td>
              <td>$ {{ $item->upgrade }}</td>
              <td>{{ $item->date }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection