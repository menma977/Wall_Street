<?php

namespace App\Http\Controllers;

use App\Models\UpgradeList;
use Illuminate\Http\Request;

class UpgradeListController extends Controller
{
  public function show()
  {
    $upgradeList = UpgradeList::all(["id", "dollar", "idr"])->sort(function ($a, $b) {
      return (int)$a->dollar > (int)$b->dollar
        ? 1 : -1;
    });
    return view("setting.upgradelist", ["upgradelist" => $upgradeList]);
  }

  public function create(Request $request)
  {
    $request->validate([
      "value" => "required|integer"
    ]);
    $upgrade = UpgradeList::first();
    $newUpgrade = new UpgradeList([
      "dollar" => $request->value,
      "idr" => $upgrade->dollar,
    ]);
    $newUpgrade->save();
    return redirect()->back();
  }

  public function update(Request $request)
  {
    $request->validate([
      "method" => ["required", "string", function ($attribute, $value, $fail) {
        if (!in_array($value, ["idrPerDollar", "updateUpgrade"])) {
          $fail('Invalid ' . $attribute . ' ' . $value);
        }
      }]
    ]);
    if ($request->input('method') == "idrPerDollar") {
      $request->validate([
        "value" => "required|integer"
      ]);
      UpgradeList::all()->each(function ($upgradeList) use ($request) {
        $upgradeList->idr = $request->value;
        $upgradeList->save();
      });
    } else {
      $request->validate([
        "type" => "required|integer|exists:upgrade_lists,id",
        "value" => "required|integer"
      ]);
      $upgradeList = UpgradeList::find($request->type);
      $upgradeList->dollar = $request->value;
      $upgradeList->save();
    }
    return redirect()->back();
  }

  public function delete(Request $request)
  {
    $request->validate([
      "type" => "required|integer|exists:upgrade_lists,id"
    ]);
    $upgradeList = UpgradeList::find($request->type);
    $upgradeList->delete();
    return redirect()->back();
  }
}
