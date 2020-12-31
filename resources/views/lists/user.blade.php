@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>User List</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Users
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">User List</h3>
        </div>
        <div class="card-body">
          <table id="custom-table">
            <thead>
              <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Verified</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>
</div>
@endsection
