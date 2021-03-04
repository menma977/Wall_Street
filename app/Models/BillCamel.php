<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BillCamel extends Model
{
  use HasFactory;

  protected $fillable = [
    "user", "type", "value", "last_try", "status"
  ];

  protected $dates = ["last_try" => "datetime:Y-m-d H:i:s"];
}
