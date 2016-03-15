<?php

namespace lde;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function join()
    {
        return $this->belongsToMany('lde\Community', 'join');
    }

    public function createdCommunities()
    {
        return $this->hasMany('lde\Community');
    }

    public function comments()
    {
        return $this->hasMany('lde\Comment');
    }
}
