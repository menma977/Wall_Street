<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShareQueue
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property string value
 * @property string type
 * @property boolean status
 */
class ShareQueue extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'value',
    'type',
    'status',
  ];
}
