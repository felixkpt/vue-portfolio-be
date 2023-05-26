<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class SkillsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "featured_image",
        "importance",
        "user_id",
        "status"
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function skills()
    {
        return $this->hasMany(Skill::class);
    }
}
