<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'posts';

    // The attributes that are mass assignable.
    protected $fillable = [
        'title',
        'content',
        'user_id', // Make sure this is the name of the foreign key column for the user.
    ];

    protected $casts = [
        'created_at' => 'datetime',
        // other casts...
    ];

    /**
     * The user who authored the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedByUser($user)
    {
        return $this->likes->contains('user_id', $user->id);
    }

    public function subpage() {
        return $this->belongsTo(Subpage::class);
    }

    public function getRouteKeyName()
{
    return 'slug';
}
}