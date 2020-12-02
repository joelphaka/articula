<?php

namespace App;

use App\Traits\Hashable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentHashids\HasHashid;

class Article extends Model
{
    use Sortable, HasHashid, Hashable {
        Hashable::getHashidsConnection insteadof HasHashid;
    }

    protected $fillable = [
        'user_id','title', 'content', 'has_cover_photo'
    ];

    protected $casts = [
        'has_cover_photo' => 'boolean'
    ];

    protected $with = ['user'];

    protected $withCount = ['likes'];

    protected $appends = [
        'hashid',
        'is_liked',
        'coverPhoto' => 'cover_photo'
    ];

    protected $sortableColumns = [
        'created_at', 'title', 'views', 'likes_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getIsLikedAttribute()
    {
        return auth()->user()->hasLiked($this);
    }

    public function getCoverPhotoAttribute()
    {
        return !$this->has_cover_photo ? null : config('filesystems.disks.articles.url') . '/' . $this->hashid().".png";
    }
}
