<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Queue
 * @package App\Models
 * @property integer id
 */
class Queue extends Model
{

  use HasFactory, SoftDeletes;

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
