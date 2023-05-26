<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $table = 'about';
    
    protected $fillable = [
        "salutation",
        "name",
        "slug",
        "slogan",
        "content",
        "content_short",
        "featured_image",
        "user_id",
        "status",
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
