<?php

namespace App\Http\Controllers;

use App\Models\ShareLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShareLevelController extends Controller
{
  public function show()
  {
    $shareLevels = ShareLevel::all(["id", "level", "percent"])->sort(function ($a, $b) {
      $startWith = function ($needle, $haystack) {
        return substr($haystack, 0, strlen($needle)) == $needle;
      };
      if ($startWith("Level", $a->level) && $startWith("Level", $b->level)) {
        $a = (int)str_replace("Level", "", $a->level);
        $b = (int)str_replace("Level", "", $b->level);
        return $a - $b;
      } else {
        return strcmp($a->level, $b->level) * -1;
      }
    });
    $randomShare = 1.0 - $shareLevels->sum("percent");
    return view("setting.sharelevel", ["shareLevels" => $shareLevels, "randomShare" => $randomShare]);
  }

  public function push()
  {
    $shareLevels = ShareLevel::all(["id", "level", "percent"]);
    $sum = $shareLevels->sum("percent");
    if ($sum >= .99)
      return redirect()->back()->withErrors("Failed, Shared total will exceed 100%");
    $min = $shareLevels->min("percent");
    $min = ($sum + $min >= 1) ? .99 - $sum : $min;
    if ($min == 0)
      return redirect()->back()->withErrors("Failed, Shared total will exceed 100%");
    $currentLevel = ShareLevel::where("level", "LIKE", "Level%")->get()->map(function ($level) {
      return (int)str_replace("Level", "", $level->level);
    })->max() + 1;
    $shareLevel = new ShareLevel([
      "level" => "Level " . $currentLevel,
      "percent" => $min
    ]);
    $shareLevel->save();
    return redirect()->back()->with("message", $shareLevel->level . " added");
  }

  public function pop()
  {
    $shareLevels = ShareLevel::where("level", "LIKE", "Level%")->get();
    if (count($shareLevels) <= 3) return redirect()->back()->withErrors("Cannot remove anymore level");
    $currentLevel = $shareLevels->map(function ($level) {
      return (int)str_replace("Level", "", $level->level);
    })->max();
    $shareLevel = ShareLevel::where("level", "Level " . $currentLevel)->first();
    $name = $shareLevel->level;
    $shareLevel->delete();
    return redirect()->back()->with("message", $name . " has been removed");
  }

  public function update(Request $request)
  {
    $request->validate([
      "id" => "required|integer|exists:share_levels,id",
      "percent" => ["required", "numeric", "gt:0", "lt:100", function ($attr, $val, $fail) use ($request) {
        $val = (float)$val / 100;
        $shareLevels = ShareLevel::all(["id", "level", "percent"]);
        $sum = $shareLevels->sum("percent");
        $selected = $shareLevels->where("id", $request->id)->first();
        $sum -= $selected->percent;
        $sum += $val;
        if ($sum >= .99)
          $fail("Failed, $attr will cause shared total to exceed 100%");
      }]
    ]);
    $shareLevel = ShareLevel::find($request->id);
    $shareLevel->percent = (float)$request->percent / 100;
    $shareLevel->save();
    return redirect()->back()->with("message", $shareLevel->level . " percent changed to " . ((float)$request->percent) . "%");
  }
}
