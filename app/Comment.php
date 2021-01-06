<?php

namespace App;

use App\Traits\Hashable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentHashids\HasHashid;

class Comment extends Model
{
    use Sortable, HasHashid, Hashable {
        Hashable::getHashidsConnection insteadof HasHashid;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'article_id' ,
        'parent_id',
        'content'
    ];

    protected $with = ['user', 'article'];

    protected $withCount = ['likes'];

    protected $appends = [
        'hashid',
        'isReply' => 'is_reply',
        'is_liked',
        'repliesCount' => 'replies_count',
    ];

    protected $sortableColumns = ['created_at'];

    /**
     * Get the article which the comment was made for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    /**
     * Get the user who made the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the owning comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentComment()
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    /**
     * Get the replies that were made for the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    /***
     * Get the likes for the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getIsReplyAttribute()
    {
        return !!$this->parentComment;
    }

    public function getIsLikedAttribute()
    {
        return auth()->user()->hasLiked($this);
    }

    public function getRepliesCountAttribute()
    {
        return $this->replies()->count();
    }
}
