<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class Doctor extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'dokter';

    protected $fillable = [
    		"kode",
            "nama",
            "email",
            "tempat_lahir",
            "tanggal_lahir",
            "gelar_depan",
            "gelar_belakang",
            "foto",
            "deskripsi",
            "verifikasi",
            "tempat_praktik",
            "penghargaan",
            "pelatihan",
            "pendidikan",
            "organisasi",
            "spesialis",
            "jadwal",
            "faskes_kode"
    	];

      public function book(){
        return $this->hasMany('Book');
      }

      public function Hospital(){
        return $this->belongsToMany('App\Hospital','kode_rs','faskes_kode');
      }
    }
