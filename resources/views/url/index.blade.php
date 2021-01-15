@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Url Engine</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Url</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">List Url Engine</h3>
        <div class="card-tools">
          <a href="{{ route('url.index') }}">
            <button type="button" class="btn btn-outline-primary btn-block btn-sm">
              <i class="fa fa-history"></i>
              Reload
            </button>
          </a>
        </div>
      </div>
      <div class="card-body p-0 table-responsive">
        <table class="table text-center">
          <thead>
          <tr>
            <th style="width: 10px">#</th>
            <th>User</th>
            <th>Created at</th>
            <th>Updated at</th>
          </tr>
          </thead>
          <tbody>
          @foreach($listUrl as $key => $item)
            <tr class="{{ $item->block ? 'bg-danger' : 'bg-success' }}">
              <td>{{ $loop->iteration }}.</td>
              <td>{{ $item->url }}</td>
              <td>{{ $item->created_at }}</td>
              <td>{{ $item->updated_at }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection