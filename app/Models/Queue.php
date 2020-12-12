<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Queue
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property integer send
 * @property string value
 * @property string type
 * @property string total
 * @property boolean status
 */
class Queue extends Model
{

  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    "user_Id",
    "send",
    "value",
    "type",
    "total",
    "status",
  ];
}
