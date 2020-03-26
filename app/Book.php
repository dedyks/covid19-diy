<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class Book extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'book';

    protected $fillable = [
    		'dokter_id',
        'user_id',
        'faskes_id',
        'user_email',
        'tgl_reservasi',
        'book_status',
        'keterangan',
        'asuransi',
        'nomor_asuransi',
        'nomorRM',
        'queue_time',
        'pembayaran',
        'status_infomedis',
        'status_pasien',
        'nomor_rekammedis',
        'keluhan',
        'riwayat_alergi',
        'riwayat_penyakit',
        'merokok'

    	];

    public function User(){
      return $this->belongsTo('App\User');
    }

    public function Doctor(){
      return $this->belongsTo('App\Doctor','dokter_id');

    }

    public function Hospital(){
      return $this->belongsTo('App\Hospital','faskes_id');
    }

    }
