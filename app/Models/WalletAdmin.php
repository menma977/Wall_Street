<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class WalletAdmin
 * @package App\Models
 * @property integer id
 * @property string wallet_btc
 * @property string wallet_doge
 * @property string wallet_ltc
 * @property string wallet_eth
 */
class WalletAdmin extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'wallet_btc',
    'wallet_doge',
    'wallet_ltc',
    'wallet_eth',
  ];
}
