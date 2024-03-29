<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CamelSetting
 * @package App\Models
 * @property integer id
 * @property string private_key
 * @property string public_key
 * @property string wallet_camel
 * @property string hex_camel
 * @property string share_value
 * @property integer share_time
 * @property double to_dollar
 */
class CamelSetting extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'private_key',
    'public_key',
    'wallet_camel',
    'hex_camel',
    'share_value',
    'share_time',
    'to_dollar',
  ];
}
