<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdvancedSettingController extends Controller
{
  public function show()
  {
    $setting = Setting::find("1");
    return view("setting.advanced", ["version" => $setting->version, "maintenance" => $setting->maintenance == 0]);
  }

  public function version(Request $request)
  {
    $request->validate([
      "version" => "required|integer"
    ]);
    $setting = Setting::find("1");
    $setting->version = $request->version;
    $setting->save();
    DB::delete("delete from oauth_access_tokens");
    return redirect()->back()->with("message", "Version changed to " . $setting->version);
  }

  public function maintenance()
  {
    $setting = Setting::find("1");
    $setting->maintenance = !$setting->maintenance;
    $setting->save();
    $statusText = $setting->maintenance ? "true" : "false";
    DB::delete("delete from oauth_access_tokens");
    return response()->json(["message" => "Status Maintenance set to $statusText", "isMaintenance" => $setting->maintenance]);
  }
}
