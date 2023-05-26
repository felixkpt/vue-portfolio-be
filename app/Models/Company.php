<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "slug", "url", 'logo',
        'position',
        'roles',
        'start_date',
        'end_date',
        "user_id",
        "status",
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
