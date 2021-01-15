<?php

namespace App\Http\Controllers;

use App\Models\ListUrl;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListUrlController extends Controller
{
  public function index()
  {
    $listUrl = ListUrl::all();
    $listUrl->map(function ($item) {
      $item->created_at = Carbon::parse($item->created_at)->format('Y-m-d H:i:s');
      $item->updated_at = Carbon::parse($item->updated_at)->format('Y-m-d H:i:s');

      return $item;
    });

    $data = [
      'listUrl' => $listUrl
    ];

    return view('url.index', $data);
  }
}
