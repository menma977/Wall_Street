<?php

namespace App\Http\Controllers;

use App\Models\QueueDailyBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QueueDailyBankController extends Controller
{
  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function update(Request $request)
  {
    $this->validate($request, [
      'private_key' => 'required',
      'public_key' => 'required',
      'wallet_camel' => 'required',
      'wallet_tron' => 'required',
    ]);

    $data = QueueDailyBank::find(1);
    $data->private_key = $request->private_key;
    $data->public_key = $request->public_key;
    $data->wallet_camel = $request->wallet_camel;
    $data->hex_camel = $request->wallet_tron;
    $data->save();

    return redirect()->back()->with(['message' => 'Bank has been updated']);
  }
}
