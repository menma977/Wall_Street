@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>Advanced Settings</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Settings
      </li>
      <li class="breadcrumb-item active">
        Advanced
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card elevation-1">
    <div class="card-header">
      <div class="card-tools">
        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
          <input type="checkbox" @if (!$maintenance) checked @endif class="custom-control-input" id="mt-switch">
          <label class="custom-control-label" for="mt-switch">
            Maintenance (<span id="mt-value">@if ($maintenance) OFF @else ON @endif</span>)
          </label>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form action="{{ route("setting.advanced.version") }}" method="post">
        @csrf
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Version</span>
          </div>
          <input type="number" class="form-control @error('record') is-invalid @enderror"
            value="{{ old("version") ?: $version }}" name="version" placeholder="Version Number">
          <div class="input-group-append">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('addJs')
<script>
  $("#mt-switch").prop('checked', {{ $maintenance ? 1 : 0 }} != 1);
  $("#mt-switch").change(e=>{
    const old = !$("#mt-switch").is(":checked")
    $.ajax({
      url: "{{ route("setting.advanced.maintenance") }}",
      type: "POST",
      headers: {
        "Accept": "application/json",
        "X-CSRF-TOKEN": $("input[name='_token']").val()
      }
    })
    .done(data => {
      $("#mt-switch").prop('checked', data.isMaintenance);
      $("#mt-value").text(data.isMaintenance?"ON":"OFF")
      console.log(data)
      Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000,
        icon: data.isMaintenance?"warning":"success",
        title: data.message
      });
    })
    .fail(({responseText}, status, thrownError) => {
      $("#mt-switch").prop('checked', old);
      $("#mt-value").text(old?"ON":"OFF")
      Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000,
        icon: "error",
        title: thrownError
      });
    })
  })
</script>
@endsection
