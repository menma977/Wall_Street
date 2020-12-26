<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShareIt
 * @package App\Models
 * @property integer id
 * @property string wallet_btc
 * @property string wallet_doge
 * @property string wallet_ltc
 * @property string wallet_eth
 * @property string private_key
 * @property string public_key
 * @property string wallet_camel
 * @property string hex_camel
 */
class ShareIt extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'wallet_btc',
    'wallet_doge',
    'wallet_ltc',
    'wallet_eth',
    'private_key',
    'public_key',
    'wallet_camel',
    'hex_camel',
  ];
}
