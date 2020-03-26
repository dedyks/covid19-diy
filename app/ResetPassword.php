<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class ResetPassword extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'resetpassword';

    protected $fillable = ['email','token'];
    protected $dates = ['valid_date'];
    
}