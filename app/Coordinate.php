<?php

namespace App;

class Coordinate
{
    public $latitude, $longitude;

    public function __construct($latitude, $longitude){
      $this->latitude=$latitude;
      $this->longitude=$longitude;
    }
}
