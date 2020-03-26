<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Counter extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'counters';

    protected $fillable = [
        'field',
        'value',
        'description'
        ];
}
