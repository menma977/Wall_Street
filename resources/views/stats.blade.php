@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>Statistics</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Stats
      </li>
      <li class="breadcrumb-item active">
        {{ $routeName }}
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-title">{{ $title }}</div>
    </div>
    <div class="card-body">
      <table id="the-table" class="table table-striped" style="overflow-x: auto; min-width: 100%">
        <thead>
          <tr>
            @foreach ($columns as $column)
            <th>{{ $column->th }}</th>
            @endforeach
          </tr>
        </thead>
      </table>
    </div>
    <div class="card-footer">
    </div>
  </div>
</div>
@endsection

@section('addCss')
<link rel="stylesheet" href="{{ asset('assets/plugin/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('addJs')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
  $("#the-table").DataTable({
    pageLength: 10,
    paging: true,
    searching: true,
    scrollX: true,
    columnDefs: @json($columnDef, JSON_PRETTY_PRINT),
    processing: true,
    serverSide: true,
    ajax: "{{ route('stats.source', [$page]) }}",
    columns: [
      @foreach ($columns as $column)
      { data: "{{ $column->label }}" },
      @endforeach
    ]
  })
</script>
@endsection
