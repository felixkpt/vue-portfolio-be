<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $table = 'about';
    
    protected $fillable = [
        "title",
        "slug",
        "slogan",
        "content",
        "content_short",
        "featured_image",
        "user_id",
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
