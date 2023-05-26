<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "slug",
        "content",
        "content_short",
        "source_uri",
        "comment_disabled",
        "featured_image",
        "status",
        "display_time",
        "importance",
        "user_id"
    ];
}
