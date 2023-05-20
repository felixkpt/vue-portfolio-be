<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "start_date",
        "level",
        "category",
        "logo",
        "importance",
        "user_id"
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
