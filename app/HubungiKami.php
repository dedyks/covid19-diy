<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HubungiKami extends Eloquent {
    protected $connection = 'mongodb';
    protected $collection = 'hubungi_kami';

    protected $fillable = ["pesan","email"];

    protected $dates = [];




    public static $rules = [
        "email" => "email|required",
        "pesan" => "required",
    ];

    public $timestamps = false;

    // Relationships

}
