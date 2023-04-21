<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

	use HasFactory;
	protected $fillable = ["module", "permission_group_id", "permissions", "routes"];
}
