<?php


namespace App\Traits;


trait Hashable
{
    public function getHashidsConnection()
    {
        return static::hashConnection();
    }

    public static function hasher()
    {
        return Hashids::connection(static::hashConnection());
    }

    protected static function hashConnection()
    {
        return config('hashids.' . static::class);
    }

    public function getHashidAttribute()
    {
        return $this->hashid();
    }
}
