<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution',
        'course',
        'qualification',
        'start_date',
        'end_date',
        "featured_image",
        'importance',
        'user_id',
        "status",
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
