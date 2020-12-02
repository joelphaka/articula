<?php

namespace App;

use App\Traits\Hashable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Mtvs\EloquentHashids\HasHashid;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasHashid, Hashable {
        Hashable::getHashidsConnection insteadof HasHashid;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'bio',
        'has_avatar',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'has_avatar' => 'boolean'
    ];

    protected $withCount = ['articles'];

    protected $appends = [
        'isAuthUser' => 'is_auth_user',
        'hashid',
        'avatar',
        'fullName' => 'full_name'
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id', 'id');
    }

    public function isAuthUser()
    {
        return auth()->check() &&
            $this->id == auth()->user()->id &&
            $this->email == auth()->user()->email &&
            $this->username == auth()->user()->username;
    }

    public function getIsAuthUserAttribute()
    {
        return $this->isAuthUser();
    }

    public function getAvatarAttribute()
    {
        return config('filesystems.disks.avatars.url') . '/' . $this->hashid().".png";
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasLiked($likeable)
    {
        return (bool)$this->likes()
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->count();
    }
}
