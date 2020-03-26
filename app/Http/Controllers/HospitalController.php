<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Coordinate;
use App\Hospital;
use App\Pelayanan;

class HospitalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $perPage=100;

    public $fasilitas, $utama, $penunjang;

    public function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function fasilitas(){
      $data = '{"utama" : [], "penunjang" : []}';
      return $data;
    }

    public function getHospital(Request $request){
      $page=$request->input('page');

      $offset=($this->perPage * ($page-1));
      $data=DB::collection('faskes')
          ->limit($this->perPage)
          ->offset($offset)
          ->get();
      return response()->json([
        'status'  => 200,
        'data'    => $data,
        'message' => 'Successfully get hospital'
      ]);
    }

    public function getHospitalById($id,Request $request){
      $data=DB::collection('faskes')
          ->where('kode_rs',$id)
          ->first();
      return response()->json([
        'status'  => 200,
        'data'    => $data,
        'message' => 'Successfully get hospital'
      ]);
    }

    public function getHospitalByIdContoh(){
      $id = '3401050';

      $data=DB::collection('faskes')
          ->where('kode_rs',$id)
          ->first();
      return response()->json([
        'status'  => 200,
        'data'    => $data,
        'message' => 'Successfully get hospital'
      ]);
    }

    public function storeHospital(Request $request){
      $kode_rs = $request->kode_rs;
      $check = Hospital::where('kode_rs',$kode_rs)->first();
      if($check == null){
        $coordinate = new Coordinate($request->latitude,$request->longitude);
        $faskes = new Hospital;
        $faskes->kode_rs = $request->kode_rs;
        $faskes->nama = $request->nama;
        $faskes->kode_telp = $request->kode_telp;
        $faskes->kode_pos = $request->kode_pos;
        $faskes->email = $request->email;
        $faskes->alamat = $request->alamat;
        $faskes->website = $request->website;
        $faskes->kota = $request->kota;
        $faskes->foto = $request->foto;
        $faskes->jenis = $request->jenis;
        $faskes->kelas = $request->kelas;
        $faskes->pemilik = $request->pemilik;
        $faskes->akreditasi = $request->akreditasi;
        $faskes->jenis_faskes = $request->jenis_faskes;
        $faskes->coordinates = $coordinate;
        $faskes->fasilitas = json_decode($this->fasilitas());
        $faskes->save();
      }else{
        return response()->json([
          'status'  => 400,
          'message' => 'Whoops, data already exist'
        ]);
      }
      return response()->json([
        'status'  => 200,
        'message' => 'Successfully added hospital',
        'data' => $faskes
      ]);
    }

    public function addFasilitas(Request $request, $id){
      $faskes = Hospital::where('kode_rs',$id)->first();

      $fasilitas = json_encode($faskes->fasilitas);
      $fasilitas = $fasilitas.'biar_string';
      $parsed_fu = $this->get_string_between($fasilitas, '{"utama":[', '],"penunjang":');
      $parsed_fp = $this->get_string_between($fasilitas, '"penunjang":[', ']}biar_string');

      if ($request->kategori == 'utama') {
        $parsed_fu_1 = str_replace("},{","|",$parsed_fu);
        $parsed_fu_2 = str_replace("{","",$parsed_fu_1);
        $parsed_fu_3 = str_replace("}","",$parsed_fu_2);
        $fasilitas_utama = explode("|",$parsed_fu_3);

        if((count($fasilitas_utama) == 1) && $fasilitas_utama[0] == ''){
          $fasilitas_utama[0] = '"nama":"'.$request->nama.'","foto":"'.$request->foto.'","deskripsi":"'.$request->deskripsi.'","tersedia":'.$request->tersedia.',"kelas":"'.$request->kelas.'","jenis_fasilitas":"'.$request->jenis_fasilitas.'"';
        }else{
          $fasilitas_tambahan = '"nama":"'.$request->nama.'","foto":"'.$request->foto.'","deskripsi":"'.$request->deskripsi.'","tersedia":'.$request->tersedia.',"kelas":"'.$request->kelas.'","jenis_fasilitas":"'.$request->jenis_fasilitas.'"';
          array_push($fasilitas_utama,$fasilitas_tambahan);
        }
        // return $fasilitas_utama;

        for ($i=0; $i < count($fasilitas_utama); $i++) {
          if ($i==0) {
            $data_to_json = '{'.$fasilitas_utama[$i];
            if ($i == (count($fasilitas_utama)-1)) {
              $data_to_json = $data_to_json.'}';
            }
          }else{
            $data_to_json = $data_to_json.'},{'.$fasilitas_utama[$i];
            if ($i == (count($fasilitas_utama)-1)) {
              $data_to_json = $data_to_json.'}';
            }
          }
        }
        $data = '{"utama":['.$data_to_json.'],"penunjang":['.$parsed_fp.']}';
        $faskes->fasilitas = json_decode($data);
        $faskes->save();
      }elseif ($request->kategori == 'penunjang') {
        $parsed_fp_1 = str_replace("},{","|",$parsed_fp);
        $parsed_fp_2 = str_replace("{","",$parsed_fp_1);
        $parsed_fp_3 = str_replace("}","",$parsed_fp_2);
        $fasilitas_penunjang = explode("|",$parsed_fp_3);

        if((count($fasilitas_penunjang) == 1) && $fasilitas_penunjang[0] == ''){
          $fasilitas_penunjang[0] = '"nama":"'.$request->nama.'","foto":"'.$request->foto.'","deskripsi":"'.$request->deskripsi.'","tersedia":'.$request->tersedia.',"kelas":"'.$request->kelas.'","jenis_fasilitas":"'.$request->jenis_fasilitas.'"';
        }else{
          $fasilitas_tambahan = '"nama":"'.$request->nama.'","foto":"'.$request->foto.'","deskripsi":"'.$request->deskripsi.'","tersedia":'.$request->tersedia.',"kelas":"'.$request->kelas.'","jenis_fasilitas":"'.$request->jenis_fasilitas.'"';
          array_push($fasilitas_penunjang,$fasilitas_tambahan);
        }
        // return $fasilitas_utama;

        for ($i=0; $i < count($fasilitas_penunjang); $i++) {
          if ($i==0) {
            $data_to_json = '{'.$fasilitas_penunjang[$i];
            if ($i == (count($fasilitas_penunjang)-1)) {
              $data_to_json = $data_to_json.'}';
            }
          }else{
            $data_to_json = $data_to_json.'},{'.$fasilitas_penunjang[$i];
            if ($i == (count($fasilitas_penunjang)-1)) {
              $data_to_json = $data_to_json.'}';
            }
          }
        }
        $data = '{"utama":['.$parsed_fu.'],"penunjang":['.$data_to_json.']}';
        $faskes->fasilitas = json_decode($data);
        $faskes->save();
      }

      return response()->json([
              'status'  => 200,
              'message' => 'Successfully added fasilitas',
              'data' => $faskes
            ]);
    }

    public function addPelayanan(Request $request, $id){
      $faskes = Hospital::where('kode_rs',$id)->first();
      $pelayanan = json_encode($faskes->pelayanan).'biar_string';
      $parsed_pumum = $this->get_string_between($pelayanan, '{"umum":[', '],"unggulan":');
      $parsed_punggulan = $this->get_string_between($pelayanan, '"unggulan":[', ']}biar_string');

      if ($request->kategori == "umum") {
        $parsed = $parsed_pumum;
      }else{
        $parsed = $parsed_punggulan;
      }
      $parsed_1 = str_replace("},{","|",$parsed);
      $parsed_2 = str_replace("{","",$parsed_1);
      $parsed_3 = str_replace("}","",$parsed_2);
      $data_pelayanan = explode("|",$parsed_3);

      $sub_pelayanan = explode(",",$request->sub_pelayanan);
      for ($i=0; $i < count($sub_pelayanan); $i++) {
        if ($i == 0) {
          $string_sub_pelayanan = '["'.$sub_pelayanan[$i].'"';
          if ($i == (count($sub_pelayanan)-1)) {
            $string_sub_pelayanan .= ']';
          }
        }else{
          $string_sub_pelayanan .= ',"'.$sub_pelayanan[$i].'"';
          if ($i == (count($sub_pelayanan)-1)) {
            $string_sub_pelayanan .= ']';
          }
        }
      }

      if((count($data_pelayanan) == 1) && $data_pelayanan[0] == ''){
        $data_pelayanan[0] = '"nama":"'.$request->nama.'","sub-pelayanan":'.$string_sub_pelayanan.'';
      }else{
        $pelayanan_tambahan = '"nama":"'.$request->nama.'","sub-pelayanan":'.$string_sub_pelayanan.'';
        array_push($data_pelayanan,$pelayanan_tambahan);
      }

      for ($i=0; $i < count($data_pelayanan); $i++) {
        if ($i==0) {
          $data_to_json = '{'.$data_pelayanan[$i];
          if ($i == (count($data_pelayanan)-1)) {
            $data_to_json = $data_to_json.'}';
          }
        }else{
          $data_to_json = $data_to_json.'},{'.$data_pelayanan[$i];
          if ($i == (count($data_pelayanan)-1)) {
            $data_to_json = $data_to_json.'}';
          }
        }
      }
      if ($request->kategori == "umum") {
        $data = '{"umum":['.$data_to_json.'],"unggulan":['.$parsed_punggulan.']}';
      }else{
        $data = '{"umum":['.$parsed_pumum.'],"unggulan":['.$data_to_json.']}';
      }

      $faskes->pelayanan = json_decode($data);
      $faskes->save();

      return response()->json([
        'status'  => 200,
        'message' => 'Successfully added pelayanan',
        'data' => $faskes
      ]);
    }

    public function addTelp(Request $request, $id){
      //telp, telp_igd, telp_reservasi;
      $kategori = $request->kategori;
      $faskes = Hospital::where('kode_rs',$id)->first()->push($kategori, $request->telp);
      $faskes = Hospital::where('kode_rs',$id)->first();
      return response()->json([
        'status'  => 200,
        'message' => 'Successfully added Telp',
        'data' => $faskes
      ]);
    }
    public function addFax(Request $request, $id){
      $faskes = Hospital::where('kode_rs',$id)->first()->push('fax', $request->fax);
      $faskes = Hospital::where('kode_rs',$id)->first();
      return response()->json([
              'status'  => 200,
              'message' => 'Successfully added Telp',
              'data' => $faskes
            ]);
    }
    public function addJam(Request $request, $id){
      $faskes = Hospital::where('kode_rs',$id)->push('jam', ['hari' => $request->hari, 'jam_buka' => $request->jam_buka, 'jam_tutup' => $request->jam_tutup], true);
      $faskes = Hospital::where('kode_rs',$id)->first();
      return response()->json([
              'status'  => 200,
              'message' => 'Successfully added Telp',
              'data' => $faskes
            ]);
    }
    public function addAsuransi(Request $request, $id){
      $faskes = Hospital::where('kode_rs',$id)->first()->push('asuransi', $request->asuransi);
      $faskes = Hospital::where('kode_rs',$id)->first();
      return response()->json([
        'status'  => 200,
        'message' => 'Successfully added Telp',
        'data' => $faskes
      ]);
    }

    public function editHospital($id,Request $request){
      $coordinate=new Coordinate($request->input('latitude'),$request->input('longitude'));
      DB::collection('faskes')->where('kode_rs',$id)->update([
        'kelas'  =>  $request->input('kelas'),
        'email'  =>  $request->input('email'),
        'alamat'  => $request->input('alamat'),
        'website'  => $request->input('website'),
        'kode_pos'  => $request->input('kode_pos'),
        'bpjs'  => $request->input('bpjs'),
        'fax'  => $request->input('fax'),
        'jenis'  => $request->input('jenis'),
        'nama'  => $request->input('nama'),
        'foto'  => $request->input('foto'),
        'kota'  => $request->input('kota'),
        'pemilik'  => $request->input('pemilik'),
        'coordinates'  => $coordinate,
        'telp'  => explode(',',$request->input('telp')),
        'telp_reservasi'  => explode(',',$request->input('telp_reservasi')),
        'telp_igd'  => explode(',',$request->input('telp_igd')),
        'pelayanan' => json_decode($request->input('pelayanan')),
      ]);

      return response()->json([
        'status'  => 200,
        'message' => 'Successfully updated hospital'
      ]);
    }

    public function getHospitalByKode($id){
      $data = DB::collection('faskes')->where('kode_rs',$id)->first();
      return response()->json([
        'status'  => 200,
        'data' => $data,
        'message' => 'Successfully'
      ]);
    }

    public function deleteHospital($id,Request $request){
      DB::collection('faskes')->where('kode_rs',$id)->delete();

      return response()->json([
        'status'  => 200,
        'message' => 'Successfully deleted hospital'
      ]);
    }
}
