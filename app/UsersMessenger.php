<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UsersMessenger extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'users_messenger';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $rules = [
           ];
    protected $fillable = [
        'user_id',
       'team_id',
       'email',
       'is_verified',
       'role',
       'code',
       'title',
       'name',
       'is_activated',
       'no_rm',
       'only_know_user_ids',
    ];
}
