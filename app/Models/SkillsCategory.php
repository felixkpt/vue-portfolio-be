<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "importance",
        "user_id"
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
