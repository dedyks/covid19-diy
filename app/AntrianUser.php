<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;


class AntrianUser extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'antrian_user';
    protected $fillable = [
        'user_id',
        'faskes_id',
        'dokter_id',
        'sesi_start',
        'sesi_end',
        'date'
    ];

    }
