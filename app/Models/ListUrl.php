<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ListUrl
 * @package App\Models
 * @property integer id
 * @property string url
 * @property boolean block
 * @property date created_at
 * @property date updated_at
 */
class ListUrl extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'url',
    'block',
    'created_at',
    'updated_at',
  ];
}
