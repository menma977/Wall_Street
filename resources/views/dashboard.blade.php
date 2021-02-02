@extends('layouts.app')

@section('title')
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Dashboard</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">Dashboard</li>
      </ol>
    </div>
  </div>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card card-outline card-primary">
      <div class="card-body">
        <div class="progress-group">
          <span class="progress-text">Queue</span>
          <span class="float-right"><b id="queueRemaining">0</b>/<b id="queueTarget">0</b></span>
          <div class="progress progress-sm">
            <div id="queueProgress" class="progress-bar bg-primary" style="width: 0"></div>
          </div>
        </div>

        <div class="progress-group">
          <span class="progress-text">Share Queue</span>
          <span class="float-right"><b id="shareRemaining">0</b>/<b id="shareTarget">0</b></span>
          <div class="progress progress-sm">
            <div id="shareProgress" class="progress-bar bg-danger" style="width: 0"></div>
          </div>
        </div>

        <div class="progress-group">
          <span class="progress-text">Not Verified User</span>
          <span class="float-right"><b>{{ $verifiedRemaining }}</b>/<b>{{ $verifiedUser }}</b></span>
          <div class="progress progress-sm">
            <div class="progress-bar bg-success" style="width: {{ $verifiedProgress }}%"></div>
          </div>
        </div>
      </div>
    </div>
    <form action="{{ route('dashboard.index') }}" method="get">
      <div class="card card-outline card-primary">
        <div class="card-body row">
          <div class="form-group col-md-5">
            <select class="custom-select" name="month">
              <option value="1" {{ 1 == $month ? 'selected' : '' }}>January</option>
              <option value="2" {{ 2 == $month ? 'selected' : '' }}>February</option>
              <option value="3" {{ 3 == $month ? 'selected' : '' }}>Mart</option>
              <option value="4" {{ 4 == $month ? 'selected' : '' }}>April</option>
              <option value="5" {{ 5 == $month ? 'selected' : '' }}>May</option>
              <option value="6" {{ 6 == $month ? 'selected' : '' }}>June</option>
              <option value="7" {{ 7 == $month ? 'selected' : '' }}>July</option>
              <option value="8" {{ 8 == $month ? 'selected' : '' }}>Augustus</option>
              <option value="9" {{ 9 == $month ? 'selected' : '' }}>September</option>
              <option value="10" {{ 10 == $month ? 'selected' : '' }}>October</option>
              <option value="11" {{ 11 == $month ? 'selected' : '' }}>November</option>
              <option value="12" {{ 12 == $month ? 'selected' : '' }}>December</option>
            </select>
          </div>
          <div class="form-group col-md-5">
            <select class="custom-select" name="year">
              @for ($i = \Carbon\Carbon::now()->addYears(-5)->format('Y'); $i <= \Carbon\Carbon::now()->addYears(5)->format('Y'); $i++)
                <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-block btn-primary">Filter</button>
          </div>
        </div>
      </div>
    </form>
    <div class="card card-outline card-primary">
      <div class="card-body">
        <div class="chart">
          <canvas id="userChart" height="180" style="height: 180px;"></canvas>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-sm-6">
            <div class="description-block border-right">
              <span class="description-percentage">
                <i class="fa fa-users"></i>
              </span>
              <h5 class="description-header">{{ $total_member }}</h5>
              <span class="description-text">Total Member</span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="description-block border-right">
              <span class="description-percentage {{ $total_member_today > 0 ? "text-success" : "text-info" }}">
                <i class="fas {{ $total_member_today > 0 ? "fa-caret-up" : "fa-caret-left" }}"></i>
                @if($total_member_today > 0)
                  {{ number_format(($total_member_today / $total_member) * 100, 2) }}%
                @else
                  0%
                @endif
              </span>
              <h5 class="description-header">{{ $total_member_today }}</h5>
              <span class="description-text">Member Today</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card card-outline card-primary">
      <div class="card-body">
        <div class="chart">
          <canvas id="profileChart" height="180" style="height: 180px;"></canvas>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-sm-6">
            <div class="description-block">
              <span class="description-percentage">
                <i class="fas fa-balance-scale"></i>
              </span>
              <h5 class="description-header">${{ $turnover }}</h5>
              <span class="description-text">Turnover</span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="description-block">
              <span class="description-percentage {{ $turnover_today > 0 ? "text-success" : "text-info" }}">
                <i class="fas {{ $turnover_today > 0 ? "fa-caret-up" : "fa-caret-left" }}"></i>
                @if($turnover_today > 0)
                  {{ number_format(($turnover_today / $turnover) * 100, 2) }}%
                @else
                  0%
                @endif
              </span>
              <h5 class="description-header">${{ $turnover_today }}</h5>
              <span class="description-text">Turnover Today</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card card-outline card-primary">
      <div class="card-body">
        <div class="chart">
          <canvas id="camelChart" height="180" style="height: 180px;"></canvas>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-sm-4 col-12">
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
                @if($total_random_share_send > 0 && $total_random_share > 0)
                  {{ number_format(($total_random_share_send / $total_random_share) * 100, 2) }}%
                @else
                  0%
                @endif
              </span>
              <h5 class="description-header">{{ $total_random_share_send }} CAMEL</h5>
              <span class="description-text">camel sent</span>
            </div>
          </div>
          <div class="col-sm-4 col-6">
            <div class="description-block">
              <span class="description-percentage {{ $total_random_share_not_send > 0 ? "text-success" : "text-info" }}">
                <i class="fas {{ $total_random_share_not_send > 0 ? "fa-caret-up" : "fa-caret-left" }}"></i>
                @if($total_random_share_not_send > 0 && $total_random_share > 0)
                  {{ number_format(($total_random_share_not_send / $total_random_share) * 100, 2) }}%
                @else
                  0%
                @endif
              </span>
              <h5 class="description-header">{{ $total_random_share_not_send }} CAMEL</h5>
              <span class="description-text">Camel Waiting</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('addJs')
  <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
  <script>
    $(function () {
      chartUser();
      chartProfit();
      chartCamel();
      getQueue();
      getShareQueue();

      setInterval(function () {
        getQueue();
        getShareQueue();
      }, 20000);
    });

    function getQueue() {
      fetch("{{ route('dashboard.queue') }}", {
        method: 'get',
        headers: new Headers({
          'Content-Type': 'application/x-www-form-urlencoded',
          "X-CSRF-TOKEN": $("input[name='_token']").val()
        }),
      }).then((response) => response.json()).then((result) => {
        $('#queueTarget').html(result.target);
        $('#queueRemaining').html(result.remaining);
        $('#queueProgress').css('width', (100 - result.progress) + '%');
      }).catch((error) => {
        $('#queueTarget').html(error);
      });
    }

    function getShareQueue() {
      fetch("{{ route('dashboard.queue.share') }}", {
        method: 'get',
        headers: new Headers({
          'Content-Type': 'application/x-www-form-urlencoded',
          "X-CSRF-TOKEN": $("input[name='_token']").val()
        }),
      }).then((response) => response.json()).then((result) => {
        $('#shareTarget').html(result.target);
        $('#shareRemaining').html(result.remaining);
        $('#shareProgress').css('width', (100 - result.progress) + '%');
      }).catch((error) => {
        $('#shareTarget').html(error);
      });
    }

    function chartUser() {
      let data = {
        labels: @json($chartUser->keys()),
        datasets: [
          {
            label: 'Users',
            backgroundColor: '#007bff',
            borderColor: '#007bff',
            pointRadius: false,
            pointColor: '#007bff',
            pointStrokeColor: '#007bff',
            pointHighlightFill: '#007bff',
            pointHighlightStroke: '#007bff',
            data: @json($chartUser->flatten()),
          },
        ]
      }

      let option = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
      };

      let chart = $('#userChart').get(0).getContext('2d')
      let chartData = jQuery.extend(true, {}, data)

      new Chart(chart, {
        type: 'bar',
        data: chartData,
        options: option
      })
    }

    function chartProfit() {
      let data = {
        labels: @json($chartUpgradeTotal->keys()),
        datasets: [
          {
            label: 'Income',
            backgroundColor: '#28a745',
            borderColor: '#28a745',
            pointRadius: false,
            pointColor: '#28a745',
            pointStrokeColor: '#28a745',
            pointHighlightFill: '#28a745',
            pointHighlightStroke: '#28a745',
            data: @json($chartUpgradeDebit->flatten())
          },
          {
            label: 'Outcome',
            backgroundColor: '#dc3545',
            borderColor: '#dc3545',
            pointRadius: false,
            pointColor: '#dc3545',
            pointStrokeColor: '#dc3545',
            pointHighlightFill: '#dc3545',
            pointHighlightStroke: '#dc3545',
            data: @json($chartUpgradeCredit->flatten())
          },
          {
            label: 'Total',
            backgroundColor: '#007bff',
            borderColor: '#007bff',
            pointRadius: false,
            pointColor: '#007bff',
            pointStrokeColor: '#007bff',
            pointHighlightFill: '#007bff',
            pointHighlightStroke: '#007bff',
            data: @json($chartUpgradeTotal->flatten()),
            type: 'bar'
          },
        ]
      }

      let option = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
      };

      let chart = $('#profileChart').get(0).getContext('2d')
      let chartData = jQuery.extend(true, {}, data)

      new Chart(chart, {
        type: 'bar',
        data: chartData,
        options: option
      })
    }

    function chartCamel() {
      let data = {
        labels: @json($chartCamel->keys()),
        datasets: [
          {
            label: 'Camel',
            backgroundColor: '#17a2b84f',
            borderColor: '#17a2b8',
            pointRadius: 3,
            pointHoverRadius: 2,
            pointColor: '#17a2b8',
            pointStrokeColor: '#17a2b8',
            pointHighlightFill: '#17a2b8',
            pointHighlightStroke: '#17a2b8',
            data: @json($chartCamel->flatten())
          },
        ]
      }

      let option = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
      };

      let chart = $('#camelChart').get(0).getContext('2d')
      let chartData = jQuery.extend(true, {}, data)

      new Chart(chart, {
        type: 'line',
        data: chartData,
        options: option
      })
    }
  </script>
@endsection