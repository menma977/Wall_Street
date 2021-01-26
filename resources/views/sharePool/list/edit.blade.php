@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Bank</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('queue.index') }}">Queue</a></li>
        <li class="breadcrumb-item"><a href="{{ route('queue.pool.index') }}">Pool</a></li>
        <li class="breadcrumb-item active">Bank</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card card-primary">
      <form method="post" action="{{ route('queue.pool.update.limit', $queue->id) }}">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="_min">MIN</label>
                <input type="text" class="form-control @error('min') is-invalid @enderror" id="_min" name="min" placeholder="Enter min" value="{{ old('min') ?: $queue->min }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="_max">MAX</label>
                <input type="text" class="form-control @error('max') is-invalid @enderror" id="_max" name="max" placeholder="Enter max" value="{{ old('min') ?: $queue->max }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="_value">Value to share</label>
                <input type="text" class="form-control @error('value') is-invalid @enderror" id="_value" name="value" placeholder="Enter value to share" value="{{ old('value') ?: $queue->value }}">
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
@endsection