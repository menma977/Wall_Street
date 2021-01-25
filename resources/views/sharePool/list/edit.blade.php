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

  </div>
@endsection