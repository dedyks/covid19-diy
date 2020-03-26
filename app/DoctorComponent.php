<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of FaskesComponent
 *
 * @author zmachmobile
 */
class DoctorComponent {
    private $doctor;

    public function __construct(){
       $this->doctor = new Doctor();
    }

    public function find($id){
        $this->doctor = Doctor::find($id);
    }

    public function save(){
        return $this->doctor->save();
    }

    public function fillUtama($request){
        $this->doctor->gelar_depan = $request->gelar_depan;
        $this->doctor->nama = $request->nama;
        $this->doctor->gelar_blk = $request->gelar_blk;
        $this->doctor->kode = $request->kode;
        $this->doctor->spesialis = $request->spesialis;
        $this->doctor->sub_spesialis = $request->sub_spesialis;
        $this->doctor->tempat_lahir = $request->tempat_lahir;
        $this->doctor->tgl_lahir = $request->tgl_lahir;
        $this->doctor->foto = $request->foto;
        $this->doctor->jenis_kelamin = $request->jenis_kelamin;
        $this->doctor->pengalaman = $request->pengalaman;
        $layanan = explode(',',$request->layanan);
        if(count($layanan) > 0){
            $local = [];
            for($i=0;$i<count($layanan);$i++){
                $layanan[$i] = trim($layanan[$i]);
            }
        }
        $this->doctor->layanan = $layanan;
        $this->doctor->deskripsi = $request->deskripsi;
        return true;
    }

    public function fillPendidikan($request){
        $pendidikan = [];
        for($i=0;$i<count($request->pendidikan_jenjang);$i++){
            $local = new \stdClass();
            $local->jenjang = $request->pendidikan_jenjang[$i];
            $local->tahun = $request->pendidikan_tahun[$i];
            $local->institusi = $request->pendidikan_institusi[$i];
            $local->gelar = $request->pendidikan_gelar[$i];
            array_push($pendidikan,$local);
        }
        $this->doctor->pendidikan = $pendidikan;
        return true;
    }

    public function fillPelatihan($request){
        $pelatihan = [];
        for($i=0;$i<count($request->pelatihan_nama);$i++){
            $local = new \stdClass();
            $local->nama = $request->pelatihan_nama[$i];
            $local->tahun = $request->pelatihan_tahun[$i];
            $local->institusi = $request->pelatihan_institusi[$i];
            array_push($pelatihan,$local);
        }
        $this->doctor->pelatihan = $pelatihan;
        return true;
    }

    public function fillPenghargaan($request){
        $penghargaan = [];
        for($i=0;$i<count($request->penghargaan_nama);$i++){
            $local = new \stdClass();
            $local->nama = $request->penghargaan_nama[$i];
            $local->tahun = $request->penghargaan_tahun[$i];
            $local->tingkat = $request->penghargaan_tingkat[$i];
            $local->institusi = $request->penghargaan_institusi[$i];
            array_push($penghargaan,$local);
        }
        $this->doctor->penghargaan = $penghargaan;
        return true;
    }

    public function fillOrganisasi($request){
        $organisasi = [];
        for($i=0;$i<count($request->organisasi_nama);$i++){
            $local = new \stdClass();
            $local->nama = $request->organisasi_nama[$i];
            $local->tahun = $request->organisasi_tahun[$i];
            $local->jabatan = $request->organisasi_jabatan[$i];
            array_push($organisasi,$local);
        }
        $this->doctor->organisasi = $organisasi;
        return true;
    }

    private function fillJadwalJam($jadwal){
        $output = [];
        foreach($jadwal as $jam){
            $local=[];
            $local['hari']=$jam['hari'][0];
            $local['mulai']=$jam['mulai'];
            $local['selesai']=$jam['selesai'];
            array_push($output,$local);
        }
        return $output;
    }

    public function fillJadwal($request){
        $jadwal = [];
        foreach($request->jammulai as $faskes){
            $local = [];
            $local['faskes_kode'] = $faskes['faskes'];
            $local['harga'] = $faskes['harga'];
            $local['jadwal'] = self::fillJadwalJam($faskes['jadwal']);
            array_push($jadwal,$local);
        }
        $this->doctor->jadwal = $jadwal;
    }

    public function fillVerifikasi($request){
        $verifikasi = [];
        for($i=0;$i<count($request->verifikasi_nama);$i++){
            $local = new \stdClass();
            $local->nama = $request->verifikasi_nama[$i];
            $local->nomor = $request->verifikasi_nomor[$i];
            $local->tahun = $request->organisasi_tahun[$i];
            $local->issuer = $request->verifikasi_issuer[$i];
            array_push($verifikasi,$local);
        }
        $this->doctor->verifikasi = $verifikasi;
        return true;
    }
}
