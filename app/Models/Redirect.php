<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $old_slug
 * @property string $new_slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Redirect extends Model
{
    use HasFactory;
}
