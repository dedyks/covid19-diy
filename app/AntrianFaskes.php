<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AntrianFaskes extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'antrian_faskes';
    protected $fillable = [
        'user_id',
        'faskes_id',
        'dokter_id',
        'sesi_start',
        'sesi_end',
        'date',
        'antrian_sekarang',
        'antrian_total',
    ];
}
