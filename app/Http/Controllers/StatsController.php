<?php

namespace App\Http\Controllers;

use App\Models\ShareQueue;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
  public function index($route)
  {
    switch ($route) {
      case "turnover":
        $result = $this->turnover($route, "Turnover", "Turnover All Time");
        break;
      case "turnover-today":
        $result = $this->turnover($route, "Turnover", "Turnover Today");
        break;
      case "upgrades-with-dividend":
        $result = $this->turnover($route, "Upgrades", "Upgrades and Shares");
        break;
      case "random-share":
        $result = $this->randomShare($route, "Random Share", "All Random Share");
        break;
      case "random-share-claimed":
        $result = $this->randomShare($route, "Random Share", "Claimed Random Share", true);
        break;
      case "random-share-unclaimed":
        $result = $this->randomShare($route, "Random Share", "Unclaimed Random Share", true);
        break;
      case "new-member":
        $result = $this->newMember($route);
        break;
      default:
        return abort(404);
    }
    return view("stats", $result);
  }

  public function source(Request $request, $route)
  {
    switch ($route) {
      case "turnover":
        $result = $this->turnoverSource($request);
        break;
      case "turnover-today":
        $result = $this->turnoverTodaySource($request);
        break;
      case "upgrades-with-dividend":
        $result = $this->turnoverSource($request, true);
        break;
      case "random-share":
        $result = $this->randomShareSource($request);
        break;
      case "random-share-claimed":
        $result = $this->randomShareSource($request, [["status", true]]);
        break;
      case "random-share-unclaimed":
        $result = $this->randomShareSource($request, [["status", false]]);
        break;
      case "new-member":
        $result = $this->newMemberSource($request);
        break;
      default:
        return [
          "draw" => (int)$request->draw,
          "recordsTotal" => 0,
          "recordsFiltered" => 0,
          "data" => [],
          "error" => "Resources Not Found!"
        ];
    }
    return response()->json($result);
  }

  private function turnover($route, $routeName, $title)
  {
    $columns = [
      new Columns("#", "id"),
      new Columns("From", "_from"),
      new Columns("To", "_to"),
      new Columns("Description", "description"),
      new Columns("Debit", "debit"),
      new Columns("Credit", "credit"),
      new Columns("Level", "level"),
      new Columns("Type", "type"),
    ];
    $colDef = [];
    return ["columns" => $columns, "columnDef" => $colDef, "routeName" => $routeName, "title" => $title, "page" => $route];
  }

  private function randomShare($route, $routeName, $title, $noClaim = false)
  {
    $columns = [
      new Columns("#", "id"),
      new Columns("Claimed", "status"),
      new Columns("User", "username"),
      new Columns("Value", "value"),
      new Columns("Date", "created_at")
    ];
    if ($noClaim) {
      array_splice($columns, 1, 1);
    }
    $colDef = [];
    return ["columns" => $columns, "columnDef" => $colDef, "routeName" => $routeName, "title" => $title, "page" => $route];
  }

  private function newMember($route)
  {
    $columns = [
      new Columns("Username", "username"),
      new Columns("Password", "password_junk"),
      new Columns("Email", "email"),
      new Columns("Phone", "phone"),
      new Columns("Join Date", "created_at"),
    ];
    $colDef = [];
    return ["columns" => $columns, "columnDef" => $colDef, "routeName" => "New Member", "title" => "New Member", "page" => $route];
  }

  private function turnoverSource(Request $request, $withDividend = false)
  {
    try {
      $searchableColumn = [];
      $upgrades = Upgrade::skip($request->start)->take($request->length);

      if ($withDividend)
        $recordsTotal = Upgrade::whereNotBetween('from', [1, 16])->whereRaw('`to` = `from`')->count();
      else
        $recordsTotal = Upgrade::whereNotBetween('from', [1, 16])->whereRaw('`to` = `from`')->where('description', 'like', '%did an upgrade')->count();

      Log::debug(Upgrade::whereNotBetween('from', [1, 16])->where('to', 'from')->count());
      foreach ($searchableColumn as $searchable) {
        $upgrades = $upgrades->orWhere($searchable, "LIKE", "%" . ($request->search["value"] ?: "") . "%");
      }
      $upgrades = $upgrades->whereNotBetween('from', [1, 16])->whereRaw('`to` = `from`');
      if (!$withDividend) {
        $upgrades = $upgrades->where('description', 'like', '%did an upgrade');
      }
      $upgrades = $upgrades->join("users as f", "f.id", "=", "upgrades.from")
        ->join("users as t", "t.id", "=", "upgrades.to")
        ->select("upgrades.*", "f.name as _from", "t.name as _to");
      $recordsFiltered = $upgrades->count();
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $upgrades->get()
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e
      ];
    }
  }

  private function turnoverTodaySource(Request $request)
  {
    try {
      $searchableColumn = [];
      $upgrades = Upgrade::skip($request->start)->take($request->length);
      $recordsTotal = Upgrade::whereNotBetween('from', [1, 16])->whereRaw('`to` = `from`')->whereRaw("DATE(NOW()) = DATE(upgrades.created_at)")->count();
      foreach ($searchableColumn as $searchable) {
        $upgrades = $upgrades->orWhere($searchable, "LIKE", "%" . ($request->search["value"] ?: "") . "%");
      }
      $upgrades = $upgrades->whereNotBetween('from', [1, 16])->whereRaw('`to` = `from`')->whereRaw("DATE(NOW()) = DATE(upgrades.created_at)");
      $upgrades = $upgrades->join("users as f", "f.id", "=", "upgrades.from")
        ->join("users as t", "t.id", "=", "upgrades.to")
        ->select("upgrades.*", "f.name as _from", "t.name as _to");
      $recordsFiltered = $upgrades->count();
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $upgrades->get()
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e
      ];
    }
  }

  private function randomShareSource(Request $request, $optionalWhere = [])
  {
    try {
      $searchableColumn = ["username"];
      $camelPrice = UpgradeList::take(1)->first();
      $share = ShareQueue::select("*");
      if ($optionalWhere)
        foreach ($optionalWhere as $optional) {
          $share = $share->where($optional[0], $optional[1]);
        }
      $recordsTotal = $share->count();
      $share = $share->whereNested(function ($q) use ($searchableColumn, $request) {
        foreach ($searchableColumn as $searchable) {
          $q->orWhere($searchable, "LIKE", "%" . ($request->search["value"] ?: "") . "%");
        }
      });
      $share = $share->join("users", "users.id", "=", "share_queues.user_id")
        ->select("share_queues.*", "users.name as username");
      $recordsFiltered = $share->count();
      $share = $share->skip($request->start)->take($request->length);
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $share->get()->map(function ($s) use ($camelPrice) {
          $s->value *= $camelPrice->camel;
          return $s;
        })
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e
      ];
    }
  }

  private function newMemberSource(Request $request)
  {
    try {
      $searchableColumn = ['username', 'email', 'phone'];
      $users = User::whereNotNull("email_verified_at");
      $users = User::whereNotNull("email_verified_at")->whereNested(function ($q) use ($searchableColumn, $request) {
        foreach ($searchableColumn as $searchable) {
          $q->orWhere($searchable, "LIKE", "%" . ($request->search["value"] ?: "") . "%");
        }
      });
      $recordsTotal = User::whereNotNull("email_verified_at")->count();
      $recordsFiltered = $users->get()->count();
      $users = $users->skip($request->start)->take($request->length);
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $users->get()->map(function ($u) {
          $u->makeVisible('password_junk');
          return $u;
        })
      ];
    } catch (Exception $e) {
      Log::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return [
        "draw" => (int)$request->draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e
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
