<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Upgrade
 * @package App\Models
 * @property integer id
 * @property integer from
 * @property integer to
 * @property string debit
 * @property string credit
 * @property integer level
 */
class Upgrade extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'from',
    'to',
    'debit',
    'credit',
    'level',
  ];
}
