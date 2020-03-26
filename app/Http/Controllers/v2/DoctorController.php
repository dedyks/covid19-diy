<?php

namespace App\Http\Controllers\v2;
use DB;
use App\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
  public function __construct()
  {

  }

  public function getDoctorById($id){
    $data=DB::collection('dokter')
        ->where('_id',$id)
        ->first();
    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'message' => 'Successfully get doctor'
    ]);
  }

  public function getDoctorByFaskes(Request $request){
    $data=DB::collection('dokter')->select('nama','gelar_depan','gelar_blk')
        ->where('jadwal.faskes_kode',$request->input('faskes_kode'))
        ->get();
    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'message' => 'Successfully get doctor'
    ]);
  }

  public function addDoctor(Request $request){
    $check=DB::collection('dokter')->where('kode',$request->input('kode'))->count();
    if($check < 1){

      $data = Doctor::create($request->all());

      return response()->json([
        'status'  => 200,
        'data'    => $data,
        'message' => 'Successfully added Doctor'
      ]);
    }else{
      return response()->json([
        'status'  => 400,
        'message' => 'Whoops, data already exist'
      ]);
    }
  }

  public function insert(Request $request) {
  
}

  public function editDetail(Request $request){
        $data = DB::collection('dokter')->where('_id',$request->input('id'))->update([
            'kode'  => $request->input('kode'),
            'nama'  =>  $request->input('nama'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'tanggal_lahir' => $request->input('tanggal_lahir'),
            'front_title' => $request->input('front_title'),
            'back_title' => $request->input('back_title'),
            'alamat' => $request->input('alamat'),
            'deskripsi' =>  $request->input('deskripsi'),
            'foto'  => $request->input('foto'),

            //di data dibawah ini database belum ada

            'telp' => $request->input('telp'),
            'suratizin_no' =>$request ->input('suratizin_no'),
            'suratizin_tahun' => $request ->input('suratizin_tahun'),
            'suratizin_oleh' => $request ->input('suratizin_oleh'),
            'pekerjaan' => $request ->input('pekerjaan'),
            'spesialis' => $request ->input('spesialis'),
            'spesialis_sub' => $request ->input('spesialis_sub')
        ]);
        return response()->json([
          'status'  => 200,
          'message' => 'Successfully ',
          'data' => $data

        ]);

    }

    public function delete($id){
      DB::collection('dokter')->where('_id',$id)->delete();

      return response()->json([
        'status'  => 200,
        'message' => 'Successfully delete doctor'
      ]);
    }




}
