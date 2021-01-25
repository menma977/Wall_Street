<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QueueDailyLimiterList
 * @package App\Models
 * @property integer id
 * @property string min
 * @property string max
 * @property string value
 */
class QueueDailyLimiterList extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'min',
    'max',
    'value',
  ];
}
