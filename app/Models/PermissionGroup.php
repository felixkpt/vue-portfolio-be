<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $fillable = ["name", "slug", "description", "routes", "slugs", "user_id", "is_default"];
}
