<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Hospital extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'faskes';

    protected $fillable = [
            '_id',
            'kode_rs',
            'nama',
            'kode_telp',
            'kode_pos',
            'email',
            'alamat',
            'website',
            'kota',
            'foto',
            'jenis',
            'kelas',
            'pemilik',
            'akreditasi',
            'jenis_faskes',
            'coordinates',
            'pelayanan',
            'fasilitas',
            'telp',
            'telp_reservasi',
            'telp_igd',
            'fax',
            'jam',
            'asuransi',
        ];

    public function book()
    {
        return $this->hasMany('Book');
    }

    public function poli()
    {
        return $this->hasMany('App\Poli', 'faskes_id');
    }

    public function doctor()
    {
        return $this->hasMany('App\Doctor', 'faskes_kode', 'kode_rs');
    }

    public function asuransi()
    {
        return $this->hasMany('Asuransi');
    }
}
