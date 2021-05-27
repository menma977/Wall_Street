<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Http;

class CamelController extends Controller
{
  static private $tronScan = "https://api.trongrid.io";

  static private $camelContract = "TXC6qE3JUBYPwwYz2wodCBBAFRALFptqDw";
  static private $camelGoldContract = "TP5eTxonM9ZomaKpZbsbEVb3zyfyL4APAj";

  public static function goldBalance($wallet)
  {
    $get = self::getInWallet($wallet);
    if ($get->code < 400) {
      return self::findBalance($get->data["data"][0]["trc20"], self::$camelGoldContract);
    }

    return 0;
  }

  public static function camelBalance($wallet)
  {
    $get = self::getInWallet($wallet);
    if ($get->code < 400) {
      return self::findBalance($get->data["data"][0]["trc20"], self::$camelContract);
    }

    return 0;
  }

  public static function tronBalance($wallet)
  {
    $get = self::getInWallet($wallet);
    if ($get->code < 400) {
      return $get->data["data"][0]["balance"];
    }

    return 0;
  }

  private static function findBalance($array, $contract)
  {
    $data = array_filter($array, function ($index) use ($contract) {
      return array_keys($index)[0] === $contract;
    });
    if (count($data) < 1) {
      return 0;
    }

    return (int)$data[array_keys($data)[0]][$contract];
  }

  private static function getInWallet($wallet)
  {
    try {
      $get = Http::get(self::$tronScan . "/v1/accounts/" . $wallet);

      switch ($get) {
        case $get->serverError():
          $data = [
            "code" => 500,
            "message" => "server error code 500",
            "data" => [],
          ];
          break;
        case $get->clientError():
          $data = [
            "code" => 401,
            "message" => "client error code 401",
            "data" => [],
          ];
          break;
        case $get->status() === 408:
          $data = [
            "code" => 408,
            "message" => "Timeout",
            "data" => [],
          ];
          break;
        case str_contains($get->body(), "error") === true:
          $data = [
            "code" => 500,
            "message" => "server has been blocked",
            "data" => [],
          ];
          break;
        case $get->status() > 200 && $get->status() < 400:
          $data = [
            "code" => $get->status(),
            "message" => "code : " . $get->status(),
            "data" => $get->json(),
          ];
          break;
        default:
          $data = [
            "code" => 200,
            "message" => "successful",
            "data" => $get->json(),
          ];
          break;
      }

      return (object)$data;
    } catch (Exception $e) {
      return (object)[
        "code" => 408,
        "message" => $e->getMessage(),
        "data" => [],
      ];
    }
  }
}
