<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class Otp extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'otp';

    protected $fillable = [
    		'kode_otp',
        'createdAt'
    	];

  protected $dateFormat = 'Y-m-d H:i:sO';
    }
