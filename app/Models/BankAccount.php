<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BankAccount
 * @package App\Models
 * @property integer id
 * @property string username
 * @property string password
 * @property string wallet_btc
 * @property string wallet_doge
 * @property string wallet_ltc
 * @property string wallet_eth
 */
class BankAccount extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'username',
    'password',
    'wallet_btc',
    'wallet_doge',
    'wallet_ltc',
    'wallet_eth',
  ];
}
