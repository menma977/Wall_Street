<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class QueueDaily
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer user_id
 * @property Boolean send
 */
class QueueDaily extends Model
{
  use HasFactory;

  protected $primaryKey = "user_id";

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'send'
  ];
}
