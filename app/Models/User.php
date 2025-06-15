<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Artwork;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

 use SoftDeletes, HasApiTokens, HasFactory, Notifiable;

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'user_id';
protected $fillable = [
    'name', 'email', 'password', 'profile_picture',
    'phone', 'location', 'bio', 'role', 'status', 'verified_artist'
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_completed' => 'boolean',
    ];

    // One-to-many with Post
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function savedPosts()
{
    return $this->belongsToMany(Post::class, 'saved_posts', 'user_id', 'post_id');
}

public function hasSavedPost($postId)
{
    return $this->savedPosts()->where('post_id', $postId)->exists();
}
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function sharedPosts()
    {
        return $this->hasMany(SharedPost::class);
    }

    // Accessor for artworks_count
    public function getArtworksCountAttribute()
    {
        return $this->posts()->count();
    }
    //reports
    public function reports()
    {
        return $this->hasMany(PostReport::class, 'user_id', 'user_id');
    }

    public function getRouteKeyName()
{
    return 'user_id';
}

}
