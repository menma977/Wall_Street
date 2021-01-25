<?php

namespace App\Http\Controllers;

use App\Models\QueueDailySetting;

class QueueDailySettingController extends Controller
{
  public function update($status)
  {
    $data = QueueDailySetting::find(1);

    if ($status == 0) {
      $data->is_on = false;
    } else {
      $data->is_on = true;
    }
    $data->save();

    if ($data->is_on) {
      return redirect()->back()->with(['message' => 'Daily Queue ON']);
    }

    return redirect()->back()->with(['message' => 'Daily Queue OFF']);
  }
}
