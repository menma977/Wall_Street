<?php

namespace App\Http\Controllers;

use App\Models\Camel;
use App\Models\HistoryCamel;
use App\Models\ShareQueue;
use App\Models\UpgradeList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HistoryCamelController extends Controller
{
  public function __construct()
  {
    $this->camelPrice = UpgradeList::find(1)->camel;
    $this->camelPrice = UpgradeList::find(1)->camel;
    $this->share = ShareQueue::whereNotBetween('user_id', [1, 16]);
    $this->camel = Camel::where('description', 'like', 'Random Share%')
      ->whereNotBetween('user_id', [1, 16]);
    $total_random_share = number_format(($this->share->where('status', false)->sum('value') * $this->camelPrice) + ($this->camel->sum('debit') / 10 ** 8), 8, '.', '');
    $total_random_share_send = number_format(($this->camel->sum('debit') / 10 ** 8), 8, '.', '');
    $total_random_share_not_send = number_format($this->share->where('status', false)->sum('value'), 8, '.', '');

    $this->common = [
      "total_random_share" => $total_random_share,
      "total_random_share_send" => $total_random_share_send,
      "total_random_share_not_send" => $total_random_share_not_send,
      "columnDef" => []
    ];
    $this->defaultColumns = [
      new Columns("Username", "username"),
      new Columns("Description", "desc"),
      new Columns("Income", "income"),
    ];
  }

  public function all()
  {
    Log::debug(array_merge($this->common, [
      "pageName" => "Combined",
      "columns" => $this->defaultColumns
    ]));
    return view("history.camel", array_merge($this->common, [
      "pageName" => "Combined",
      "columns" => $this->defaultColumns
    ]));
  }

  public function sent()
  {
    return view("history.camel", array_merge($this->common, [
      "pageName" => "Sent",
      "columns" => $this->defaultColumns
    ]));
  }
  public function pending()
  {
    return view("history.camel", array_merge($this->common, [
      "pageName" => "Not Sent",
      "columns" => $this->defaultColumns
    ]));
  }

  public function sources(Request $request, $type)
  {
    $start = $request->start;
    $length = $request->length;
    $search = $request->search["value"] ?: "";
    switch ($type) {
      case "combined":
        return response()->json(array_merge(
          ["draw" => (int)$request->draw],
          $this->allSource($start, $length, $search)
        ));
        break;
      case "sent":
        return response()->json(array_merge(
          ["draw" => (int)$request->draw],
          $this->sentSource($start, $length, $search)
        ));
        break;
      case "not-sent":
        return response()->json(array_merge(
          ["draw" => (int)$request->draw],
          $this->notSentSource($start, $length, $search)
        ));
        break;
      default:
        abort(404);
    }
  }

  private function allSource($start, $length, $search)
  {
    try {
      // SENT ==================================================
      $camel = $this->camel
        ->join("users", "users.id", "=", "camels.user_id")
        ->select("camels.*", "users.username as username");
      $total = $camel->count();
      $camel = $camel->whereNested(function ($q) use ($search) {
        $q->orWhere("username", "LIKE", "%" . $search . "%")
          ->orWhere("description", "LIKE", "%" . $search . "%");
      });
      $filtered = $camel->count();
      $camel = $camel->get()->map(function ($item) {
        $item->income = $item->debit / 10 ** 8;
        $item->desc = $item->description;
        return $item;
      });
      // NOT SENT ==============================================
      $share = $this->share
        ->where('status', false)
        ->join("users", "users.id", "=", "share_queues.user_id")
        ->select("share_queues.*", "users.username as username");
      $total += $share->count();
      $share = $share->whereNested(function ($q) use ($search) {
        $q->orWhere("username", "LIKE", "%" . $search . "%");
      });
      $filtered += $share->count();
      $share = $share->get()->map(function ($item) {
        $item->income = $item->debit / 10 ** 8;
        $item->desc = $item->description;
        return $item;
      });
      // MERGING ===============================================
      $data = $camel->merge($share)->sortBy("created_at")->splice($start, $start + $length);
      return [
        "recordsTotal" => $total,
        "recordsFiltered" => $filtered,
        "data" => $data->toArray()
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Something happen when fetching the data"
      ];
    }
  }

  private function sentSource($start, $length, $search)
  {
    try {
      $camel = $this->camel
        ->join("users", "users.id", "=", "camels.user_id")
        ->select("camels.*", "users.username as username");
      $total = $camel->count();
      $camel = $camel->whereNested(function ($q) use ($search) {
        $q->orWhere("username", "LIKE", "%" . $search . "%")
          ->orWhere("description", "LIKE", "%" . $search . "%");
      });
      $filtered = $camel->count();
      $camel = $camel->skip($start)->take($length);
      return [
        "recordsTotal" => $total,
        "recordsFiltered" => $filtered,
        "data" => $camel->get()->map(function ($item) {
          $item->income = $item->debit / 10 ** 8;
          $item->desc = $item->description;
          return $item;
        })
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Something happen when fetching the data"
      ];
    }
  }

  private function notSentSource($start, $length, $search)
  {
    try {
      $share = $this->share
        ->where('status', false)
        ->join("users", "users.id", "=", "share_queues.user_id")
        ->select("share_queues.*", "users.username as username");
      $total = $share->count();
      $share = $share->whereNested(function ($q) use ($search) {
        $q->orWhere("username", "LIKE", "%" . $search . "%");
      });
      $filtered = $share->count();
      $share = $share->skip($start)->take($length);
      return [
        "recordsTotal" => $total,
        "recordsFiltered" => $filtered,
        "data" => $share->get()->map(function ($item) {
          $item->income = $item->value;
          $item->desc = "Waiting to be send to " . $item->username;
          return $item;
        })
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Something happen when fetching the data"
      ];
    }
  }
}

class Columns
{
  public $th;
  public $label;

  function __construct($th, $label)
  {
    $this->th = $th;
    $this->label = $label;
  }
}
