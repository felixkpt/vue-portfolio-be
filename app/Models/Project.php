<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Project extends Model
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
        "user_id",
        "project_url",
        "github_url",
        "company_id",
        "start_date",
        "end_date",
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
    function company()
    {
        return $this->belongsTo(Company::class);
    }

    function skills()
    {
        return $this->belongsToMany(Skill::class);
    }
}
