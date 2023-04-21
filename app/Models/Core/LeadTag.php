<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTag extends Model
{

	use HasFactory;
	protected $fillable = ["lead_id","tag_id", "tag"];

}
