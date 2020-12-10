<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 * @property integer id
 * @property integer role
 * @property string name
 * @property string username
 * @property string email
 * @property string email_verified_at
 * @property string phone
 * @property string password
 * @property string password_junk
 * @property string secondary_password
 * @property string secondary_password_junk
 * @property string username_doge
 * @property string password_doge
 * @property string cookie
 * @property string wallet_btc
 * @property string wallet_ltc
 * @property string wallet_doge
 * @property string wallet_eth
 * @property integer level
 * @property boolean suspend
 */
class User extends Authenticatable implements MustVerifyEmail
{
  use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'role',
    'name',
    'username',
    'email',
    'phone',
    'password',
    'password_junk',
    'secondary_password',
    'secondary_password_junk',
    'username_doge',
    'password_doge',
    'cookie',
    'wallet_btc',
    'wallet_ltc',
    'wallet_doge',
    'wallet_eth',
    'level',
    'suspend',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'password_junk',
    'secondary_password',
    'secondary_password_junk',
    'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];
}
