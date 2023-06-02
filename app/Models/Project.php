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
        return $this->hasOne(User::class, '_id', 'user_id');
    }
    function company()
    {
        return $this->hasOne(Company::class, '_id', 'company_id');
    }

    function skills()
    {
        return $this->belongsToMany(Skill::class);
    }
}
