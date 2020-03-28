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
        'nama' => 'required',
        'no_hp' => 'required',
           ];
    protected $fillable = [
        'faskes_name',
        'questionnaire',
        'nama',
        'alamat',
        'no_hp',
        'kesimpulan'
    ];
}
