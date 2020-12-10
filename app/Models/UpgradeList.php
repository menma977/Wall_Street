<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UpgradeList
 * @package App\Models
 * @property string dollar
 * @property string idr
 * @property string btc
 * @property string ltc
 * @property string doge
 * @property string eth
 * @property string btc_usd
 * @property string ltc_usd
 * @property string doge_usd
 * @property string eth_usd
 */
class UpgradeList extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'dollar',
    'idr',
    'btc',
    'ltc',
    'doge',
    'eth',
    'btc_usd',
    'ltc_usd',
    'doge_usd',
    'eth_usd',
  ];
}
