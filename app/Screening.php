<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Screening extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'screening';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $rules = [
           ];
    protected $fillable = [
        'faskes_name',
        'id_faskes',
        'questionnaire',
        'nama',
        'data',
        'alamat',
        'no_hp',
        'kesimpulan',
    ];
}
