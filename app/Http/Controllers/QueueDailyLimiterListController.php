<?php

namespace App\Http\Controllers;

use App\Models\QueueDailyLimiterList;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QueueDailyLimiterListController extends Controller
{
  /**
   * @param $id
   * @return Application|Factory|View
   */
  public function edit($id)
  {
    $queue = QueueDailyLimiterList::find($id);

    $data = [
      'queue' => $queue,
    ];

    return view('sharePool.list.edit', $data);
  }

  public function update($id, Request $request)
  {
    $this->validate($request, [
      'min' => 'required|numeric|min:1',
      'max' => 'required|numeric|min:1',
      'value' => 'required|numeric|min:0.00000001',
    ]);

    $data = QueueDailyLimiterList::find($id);
    $data->min = $request->min;
    $data->max = $request->max;
    $data->value = $request->value;
    $data->save();

    return redirect()->route('queue.pool.index')->with(['message' => 'Limit has been updated']);
  }
}
