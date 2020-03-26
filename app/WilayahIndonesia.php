<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class WilayahIndonesia extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'wilayah_indonesia';

    protected $fillable = [


        ];
    
    public $timestamps = true;

    }
