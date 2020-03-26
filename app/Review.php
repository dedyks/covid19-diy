<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class Review extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'review';

    protected $fillable = [
            "user_id",
            "faskes_id",
            "rating",
            "review"

        ];
    
    public $timestamps = true;

    }
