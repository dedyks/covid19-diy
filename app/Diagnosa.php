<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Diagnosa extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'diagnosa';

    protected $fillable = ['Kode',
    'Diagnosa',
    'Deskripsi'

    ];

    protected $dates = [];

    public static $rules = [
        "Kode" => "required",


    ];

    public $timestamps = false;

    // Relationships

}
