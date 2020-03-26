<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class RequestDemo extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'request_demo';

    protected $fillable = ["name","email","telp","nama_faskes","jenis_tempat_praktek","role"];

    public static $rules = [
        "email" => "email|required",
        "name" => "required"
    ];


    public $timestamps = false;

    // Relationships

}
