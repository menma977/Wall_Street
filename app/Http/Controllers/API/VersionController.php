<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class VersionController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    $setting = Setting::find(1);
    $data = [
      'maintenance' => $setting->maintenance,
      'version' => $setting->version,
    ];

    return response()->json($data);
  }
}
