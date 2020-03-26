<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;
class ImageModel extends Eloquent {

    
    protected $connection = 'mongodb';
    protected $collection = 'image';

    protected $fillable = [
       'user_id',
       'imageName',
       'imagePath',
       'imageDimension'


    	];
    protected $guarded = []; //tambahkan baris ini
    public static $rules = [

	];

    protected $dates = [

        'deleted_at'
    ];

      public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
