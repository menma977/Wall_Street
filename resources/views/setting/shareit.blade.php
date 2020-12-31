@extends('layouts.app')

@section('title')
<div class="row mb-2">
  <div class="col-sm-6">
    <h1>Share Cut Settings</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item active">
        Settings
      </li>
      <li class="breadcrumb-item active">
        Share Cut
      </li>
    </ol>
  </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-8">
      <div>
        <div class="card">
          <div class="card-header ui-sortable-handle">
            <h3 class="card-title">Change Setting</h3>
            <div class="card-tools">
              <ul class="nav nav-pills ml-auto">
                <li class="nav-item">
                  <a href="edit-upgrade-container" class="nav-link active" data-toggle="tab">Edit</a>
                </li>
                <li class="nav-item">
                  <a href="#create-upgrade" class="nav-link" data-toggle="tab">Create</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content p-0">
              <div id="edit-upgrade-container" class="tab-pane active">
                <form role="form" id="edit-delete-upgrade" name="edit-delete-upgrade" method="POST">
                  @csrf
                  <input type="hidden" name="method" id="edit-delete-method" value="updateUpgrade" />
                  <div class="form-group">
                    <label>Upgrades</label>
                    <div class="input-group">
                      <select name="type" id="edit-upgrade-type" class="custom-select">
                        @foreach ($upgradelist as $item)
                        <option value="{{ $item->id }}">{{ $item->dollar }}</option>
                        @endforeach
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-danger" id="delete-upgrade" type="submit">Delete</button>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="">New Base Price</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fas fa-dollar-sign"></i>
                        </span>
                      </div>
                      <input id="edit-upgrade-dollar" name="value" type="text" class="form-control">
                      <div class="input-group-append">
                        <button class="btn btn-warning" id="edit-upgrade" type="submit"><i
                            class="fas fa-pencil-alt"></i> Edit</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div id="create-upgrade" class="tab-pane">
                <form role="form" method="POST" id="create-level" action="{{ route("setting.upgrade-list.create") }}">
                  @csrf
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-dollar-sign"></i>
                      </span>
                    </div>
                    <input type="text" name="value" class="form-control">
                    <div class="input-group-append">
                      <button class="btn btn-success"><i class="fas fa-plus"></i> Create</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="card-footer">
          </div>
        </div>
      </div>
      <div>
        <div class="card">
          <div class="card-header">
            <div class="card-title">
              Rupiah per Dollar
            </div>
          </div>
          <div class="card-body">
            <form role="form" method="POST" action="{{ route("setting.upgrade-list.edit") }}">@csrf
              <input type="hidden" name="method" value="idrPerDollar" />
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      IDR
                    </span>
                  </div>
                  <input type="text" name="value" value="{{ $upgradelist[0]->idr }}" class="form-control">
                  <div class="input-group-append">
                    <button class="btn btn-success"><i class="fas fa-pencil-alt"></i> Edit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Overview</h3>
        </div>
        <div class="card-body">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Base Price (USD)</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($upgradelist as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->dollar }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@section("addJs")
<script>
  const rawData = @json($upgradelist);
  const data = Object.keys(rawData).map((v) => rawData[v]);

  $("#edit-upgrade-type").change(e=>{
    $("#edit-upgrade-dollar").val(data.filter(v=>v.id == e.target.value)[0].dollar);
  })

  $("#edit-upgrade-dollar").val(data.filter(v=>v.id == $("#edit-upgrade-type").val())[0].dollar);

  const form = document.querySelector("#edit-delete-upgrade");

  document.querySelector("#delete-upgrade").addEventListener("click", e=>{
    e.preventDefault();
    form.action = "{{ route("setting.upgrade-list.delete") }}";
    form.submit();
  })

  document.querySelector("#edit-upgrade").addEventListener("click", e=>{
    e.preventDefault();
    form.action = "{{ route("setting.upgrade-list.edit") }}";
    form.submit();
  })

</script>
@endsection
