<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Upgrade
 * @package App\Models
 * @property integer id
 */
class Upgrade extends Model
{
    use HasFactory, SoftDeletes;
}
