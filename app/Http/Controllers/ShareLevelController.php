<?php

namespace App\Http\Controllers;

use App\Models\ShareLevel;
use Illuminate\Http\Request;

class ShareLevelController extends Controller
{
  public function show()
  {
    $shareLevels = ShareLevel::all();
    return view("setting.sharelevel", ["shareLevels" => $shareLevels]);
  }

  public function upgrade(Request $request)
  {
  }
}
