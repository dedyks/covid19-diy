<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Poli extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'poli';
    public $service;

    public function hospital()
    {
        return $this->belongsTo('App/Hospital', 'faskes_id');
    }
}
