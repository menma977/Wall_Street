@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>History Camel</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        History Camel
      </li>
      <li class="breadcrumb-item active">
        {{ $pageName }}
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4 col-6">
      <div class="description-block border-right">
        <span class="description-percentage">
          <i class="fas fa-balance-scale"></i>
        </span>
        <h5 class="description-header">{{ $total_random_share }} CAMEL</h5>
        <span class="description-text">Total Share</span>
      </div>
    </div>
    <div class="col-sm-4 col-6">
      <div class="description-block border-right">
        <span class="description-percentage {{ $total_random_share_not_send > 0 ? "text-success" : "text-info" }}">
          <i class="fas {{ $total_random_share_not_send > 0 ? "fa-caret-up" : "fa-caret-left" }}"></i>
          {{ number_format(($total_random_share_send / $total_random_share) * 100, 2) }}%
        </span>
        <h5 class="description-header">{{ $total_random_share_send }} CAMEL</h5>
        <span class="description-text">camel sent</span>
      </div>
    </div>
    <div class="col-sm-4 col-6">
      <div class="description-block">
        <span class="description-percentage {{ $total_random_share_not_send > 0 ? "text-success" : "text-info" }}">
          <i class="fas {{ $total_random_share_not_send > 0 ? "fa-caret-up" : "fa-caret-left" }}"></i>
          {{ number_format(($total_random_share_not_send / $total_random_share) * 100, 2) }}%
        </span>
        <h5 class="description-header">{{ $total_random_share_not_send }} CAMEL</h5>
        <span class="description-text">Camel Waiting</span>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <div class="card-title"></div>
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
  $.fn.dataTableExt.sErrMode = function(e, _, message){
    console.log(message);
    Swal.fire({
      icon: "error",
      title: message.substr(`DataTables warning: table id=${e.sTableId} - `.length)
    })
  }
  $("#the-table").DataTable({
    pageLength: 10,
    paging: true,
    searching: true,
    scrollX: true,
    columnDefs: @json($columnDef, JSON_PRETTY_PRINT),
    processing: true,
    serverSide: true,
    ajax: "{{ route('history.camel.source', [str_replace([' '],'-',strtolower($pageName))]) }}",
    columns: [
      @foreach ($columns as $column)
      { data: "{{ $column->label }}" },
      @endforeach
    ]
  })
</script>
@endsection
