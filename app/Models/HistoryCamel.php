<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoryCamel
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property integer wallet
 * @property string code
 * @property string value
 */
class HistoryCamel extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'wallet',
    'code',
    'value',
  ];
}
