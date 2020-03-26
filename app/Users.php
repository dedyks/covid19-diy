<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;


class Users extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'user';

    protected $fillable = [
        'sex',
        'fullName',
        'email',
        'password',
        'salt',
        'telp',
        'sekolah',
        'role',
        'asessor'
        
        ];

    protected $hidden = [
        'password'
    ];
    public function image(){
        return $this->hasMany('App\ImageModel','user_id');
    }
        

   

    }
