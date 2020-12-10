<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Doge
 * @package App\Models
 * @property integer user_id
 * @property string description
 * @property integer debit
 * @property integer credit
 */
class Doge extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'description',
    'debit',
    'credit',
  ];
}
