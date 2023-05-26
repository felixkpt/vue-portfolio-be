<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Permission extends Model
{

	use HasFactory;
	protected $fillable = ["module", "permission_group_id", "permissions", "routes"];
}
