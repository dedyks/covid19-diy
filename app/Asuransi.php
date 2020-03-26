<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class Asuransi extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'asuransi';

    protected $fillable = [
    		'nama',
        'alias'
    	];

    public static $rules = [
      		"nama" => "required",
      		"alias" => "required"
      	];
  protected $dateFormat = 'Y-m-d H:i:sO';
    

    public function Hospital(){
      return $this->belongsTo('App\Hospital','nama');
    }
  }
