<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likeable';

    protected $fillable = [
        'likeable_id',
        'likeable_type',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likeable()
    {
        return $this->morphTo()->first();
    }
}
