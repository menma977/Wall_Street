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
              <a href="{{ route("setting.share-level.pop") }}" class="btn btn-danger">
                <i class="fas fa-minus"></i> Level
              </a>
              <a href="{{ route("setting.share-level.push") }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Level
              </a>
            </div>
          </div>
          <div class="card-body">
            <form role="form" action="{{ route("setting.share-level.update") }}" method="POST">
              @csrf
              <div class="form-group">
                <label>Levels</label>
                <select name="id" id="edit-level-type" class="custom-select">
                  @foreach ($shareLevels as $item)
                  <option value="{{ $item->id }}">{{ $item->level }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="">Base Percentage</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fas fa-percentage"></i>
                    </span>
                  </div>
                  <input id="edit-level" name="percent" type="text" class="form-control">
                  <div class="input-group-append">
                    <button class="btn btn-warning" type="submit"><i class="fas fa-pencil-alt"></i> Edit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="card-footer">
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
                <th>Name</th>
                <th>Percent Share</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($shareLevels as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->level }}</td>
                <td class="text-right">{{ $item->percent * 100 }}%</td>
              </tr>
              @endforeach
              <tr>
                <td>{{ count($shareLevels)+1 }}</td>
                <td>Random Share</td>
                <td class="text-right">{{ $randomShare * 100 }}%</td>
              </tr>
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
  const rawData = @json($shareLevels);
  const data = Object.keys(rawData).map((v) => rawData[v]);

  $("#edit-level-type").change(e=>{
    $("#edit-level").val(parseFloat(data.filter(v=>v.id == e.target.value)[0].percent) * 100);
  })

  $("#edit-level").val(parseFloat(data.filter(v=>v.id == $("#edit-level-type").val())[0].percent) * 100);

</script>
@endsection
