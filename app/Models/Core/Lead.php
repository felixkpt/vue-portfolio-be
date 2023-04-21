<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    
	use HasFactory;
	protected $fillable = ["lead_category_id","name","email","phone","description"];

}
