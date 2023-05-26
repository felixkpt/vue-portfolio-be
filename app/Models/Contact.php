<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'link',
        'logo',
        'importance',
        'user_id',
        "status",
    ];
    
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
