<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Binary
 * @package App\Models
 * @property integer id
 * @property integer sponsor
 * @property integer up_line
 * @property integer down_line
 * @property boolean active
 * @property string profit
 */
class Binary extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'sponsor',
    'up_line',
    'down_line',
    'active',
    'profit',
  ];
}
