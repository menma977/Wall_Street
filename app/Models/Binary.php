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
 * @property integer upline
 * @property integer downline
 * @property boolean active
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
    'upline',
    'downline',
    'active',
  ];
}
