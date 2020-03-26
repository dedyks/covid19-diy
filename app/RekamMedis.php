<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class RekamMedis extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'rekammedis';

    protected $fillable = [
    		    "user_email",
            "tinggi",
            "berat",
            "sistole",
            "diastole",
            "gol_darah",
            "keluhab",
            "heart_rate",
            "respirasi",
            "suhu",
            "alergi"

    	];
    }
