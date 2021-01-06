<?php

namespace App;

use App\Traits\Hashable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
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

    protected $withCount = ['likes', 'comments'];

    protected $appends = [
        'hashid',
        'is_liked',
        'coverPhoto' => 'cover_photo',
        'tileId' => 'title_id',
    ];

    protected $sortableColumns = [
        'created_at', 'title', 'views', 'likes_count'
    ];

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

    public function getTitleIdAttribute()
    {
        $titleWithoutSpecialChars = preg_replace('/[^0-9A-Za-z]+/', '-', $this->title);
        $titleWithoutSpecialChars = strtolower(trim($titleWithoutSpecialChars, '-'));
        $dateCreated = Carbon::parse($this->created_at)->format('Ymd');
        $hashId = $this->hashid();

        return "{$titleWithoutSpecialChars}-{$dateCreated}-{$hashId}";
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            // We're receiving a numerical value so we invoke the default implementation
            return parent::resolveRouteBinding($value, $field);
        } else {
            // Pattern of an article title id:
            // (title with special chars replaced by dash)-(date:Ymd)-(hashid)
            $pattern = '/([A-Za-z0-9][A-Za-z0-9\-]{0,117}[A-Za-z0-9])\-([0-9]{8})\-([A-Za-z0-9]{8})/';

            $matchResult = [];

            if (preg_match($pattern, $value, $matchResult)) {
                // Retrieve the model hashid from matched result
                $hashId = $matchResult[3];
                // Find a model with the hashId
                $article = self::findByHashidOrFail($hashId);

                // Check if a model is found and the model's title id matches the value from the route
                if ($article && $article->title_id == $value) {
                    return  $article;
                }
            }

            // No model found
            throw (new ModelNotFoundException)->setModel(
                Article::class, $value
            );
        }
    }
}
