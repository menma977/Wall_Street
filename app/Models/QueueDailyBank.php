<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QueueDailyBank
 * @package App\Models
 * @property integer id
 * @property string private_key
 * @property string public_key
 * @property string wallet_camel
 * @property string hex_camel
 */
class QueueDailyBank extends Model
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
  ];
}
