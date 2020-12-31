@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>Wallet Admin</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Settings
      </li>
      <li class="breadcrumb-item active">
        Wallet Admin
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Lists</div>
      <div class="card-tools">
        <div class="nav nav-pills ml-auto">
          <a href="{{ route("setting.wallet-admin.create") }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Wallet Admin
          </a>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table id="wallet-admin-table" class="table table-striped" style="overflow-x: auto">
        <thead>
          <tr>
            <th style="width: 3rem">#</th>
            <th>Name</th>
            <th>Wallet Camel</th>
            <th>Wallet Bitcoin</th>
            <th>Wallet Litecoin</th>
            <th>Wallet Ethereum</th>
            <th>Wallet Dogecoin</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($walletAdmin as $wallet)
          <tr>
            <td style="width: 3rem">{{ $loop->iteration }}</td>
            <td>{{ $wallet->name }}</td>
            <td>{{ $wallet->wallet_camel }}</td>
            <td>{{ $wallet->wallet_btc }}</td>
            <td>{{ $wallet->wallet_ltc }}</td>
            <td>{{ $wallet->wallet_eth }}</td>
            <td>{{ $wallet->wallet_doge }}</td>
            <td class="wrapper text-center">
              <div class="button-group" style="width: 13rem">
                <a href="{{ route("setting.wallet-admin.edit", ["id"=>Illuminate\Support\Facades\Crypt::encrypt($wallet->id)]) }}"
                  class="btn btn-warning">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <a data-wallet="{{ Illuminate\Support\Facades\Crypt::encrypt($wallet->id) }}"
                  data-name="{{ $wallet->name }}" class="wallet-delete btn btn-danger">
                  <i data-wallet="{{ Illuminate\Support\Facades\Crypt::encrypt($wallet->id) }}"
                    data-name="{{ $wallet->name }}" class="fas fa-trash-alt"></i> Delete
                </a>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
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
  $("#wallet-admin-table").DataTable({
    pageLength: 10,
    paging: true,
    searching: true,
    scrollX: true,
    columnDefs: [
      { targets: [2,3,4,5,6,7], orderable: false},
      { targets: [0, 1], orderable: true},
      { targets: [0,2,3,4,5,6,7], searchable: false}
    ]
  })

  $(".wallet-delete").click(async e=>{
    const r = await Swal.fire({
      title: "Irreversible acion!",
      text: `Are you sure want to delete ${e.target.dataset.name} ?`,
      icon: "error",
      showConfirmButton: true,
      showCancelButton: true,
      confirmButtonText: "No, take me back",
      cancelButtonText: "Yes, i'm sure",
    })
    if(r.dismiss && r.dismiss == 'cancel'){
      location.href = `{{ route("setting.wallet-admin.delete") }}/${e.target.dataset.wallet}`
    }
  })
</script>

@endsection
