<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Eloquent implements AuthenticatableContract, AuthorizableContract
{
    protected $connection = 'mongodb';
    protected $collection = 'users';

    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     public static $rules = [

       	];
    protected $fillable = [
        'user_id',
        'imageName',
        'imageDimension',
        'imagePath',
        'firstName',
        'lastName',
        'email',
        'salt',
        'role',
        'telp',
        'jenisKelamin',
        'tempatLahir',
        'tglLahir',
        'alamat',
        'statusNikah',
        'pekerjaan',
        'pendidikanTerakhir',
        'kesehatan',
        'nomorRM',
        'asuransi',
        'faskes_id_pendaftar',
        'noKTP',
        'favorit',
        'hubungan',
        'user_id',
        'verified',
        'hash',
        'login_token',
        'keterangan' 
    ];

    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

   



}
