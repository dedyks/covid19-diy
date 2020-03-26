<?php

namespace App\Http\Controllers\Search;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctor;
use App\Hospital;
use App\User;
use DB;
use Carbon\Carbon;

class SearchDoctorController extends Controller
{
    // $rumah_sakit_field = [	'id','kode','nama','jenis', 'kelas', 'alamat', 'kota', 'kode_pos', 'telepon_igd', 'fax', 'email', 'telp_humas', 'telp_reservasi', 'website', 'pelayanan', 'foto', 'coordinates', 'bpjs'];
    public function index()
    {
        $data = Doctor::paginate(10);
        return response()->json([
        'status'  => 200,
        'message' => 'Endpoint aktif',
        'data' => $data
      ]);
    }





    public function search_doctor(Request $request)
    {
        $nama = $request->input('nama');
        $jadwal_hari = $request->input('jadwal_hari');
        $lokasi = $request->input('lokasi');
        $jenis_kelamin = $request->input('jenis_kelamin');
        $spesialisasi = $request->input('spesialisasi');
        $harga_mulai = $request->input('harga_mulai');

        //	$data = DB::collection('faskes')->where('jam.hari','Selasa')->paginate(10);



        $data = DB::collection('dokter')
    
        ->join('faskes', 'dokter.jadwal.faskes_kode', '=', 'faskes.kode_rs')
    
        ->when($nama, function ($query, $nama) {
          return $query->where('nama', 'like', '%'.$nama.'%');
      })

     ->when($jadwal_hari, function ($query, $jadwal_hari) {
         return $query->where('jam.hari', $jadwal_hari);
     })

     ->when($lokasi, function ($query, $lokasi) {
         return $query->where('kota', 'like', '%'.$lokasi.'%');
     })

     ->when($jenis_kelamin, function ($query, $jenis_kelamin) {
         return $query->where('jenis_kelamin', 'like', '%'.$jenis_kelamin.'%');
     })

            ->when($spesialisasi, function ($query, $spesialisasi) {
                return $query->where('spesialis', 'like', '%'.$spesialisasi.'%');
            })

      ->when($harga_mulai, function ($query, $harga_mulai) {
          return $query->where('jadwal.harga', '<=', $harga_mulai);
      })
      ->orderBy('nama','asc')

      ->paginate(10);

        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully get doctor',

            ]);
        // where([
            // 		['nama', 'like', '%'.$nama.'%']
            //
            // ])
    }

    public function search_doctor_spesialis(Request $request)
    {
        $nama = $request->input('nama');
        $gelar_depan = $request->input('gelar_depan');
        $lokasi = $request->input('lokasi');
        $spesialisasi = $request->input('spesialisasi');



        $data = DB::collection('dokter')

      ->when($gelar_depan, function ($query, $gelar_depan) {
          return $query->where('nama', $gelar_depan);
      })
      ->when($nama, function ($query, $nama) {
          return $query->where('nama', 'like', '%'.$nama.'%');
      })

     ->when($lokasi, function ($query, $lokasi) {
         return $query->where('kota', 'like', '%'.$lokasi.'%');
     })

      ->when($spesialisasi, function ($query, $spesialisasi) {
          return $query->where('spesialis', 'like', '%'.$spesialisasi.'%');
      })



      ->paginate(10);

        return response()->json([
        'status'  => 200,
        'data'    => $data,
        'message' => 'Successfully get doctor spesialis',

      ]);
        // where([
      // 		['nama', 'like', '%'.$nama.'%']
      //
      // ])
    }



    public function search_longlat(Request $request, $longlat)
    {
        $data = explode("&&", $longlat);
        if (count($data) == 2) {
            // program jalan
            $plongtitude = $data[0];
            $platitude = $data[1];
            $doctor = Hospital::all();
            foreach ($doctor as $key => $doctor) {
                $latitude = $doctor['coordinates']['latitude'];
                $latitude = $latitude - $platitude;
                $longitude = $doctor['coordinates']['longitude'];
                $longitude = $longitude - $platitude;
                $doctor['rata'] = ($latitude + $longitude) / 2;
            }
        } else {
            return redirect()->back();
        }
    }

    public function check_data()
    {
        $doctor = Hospital::find('5a27d341b7187a6f4bde3ddd');
        $i = 1;
        // foreach ($doctor as $key => $doctor) {
        //     // echo $doctor['pelayanan'];
        //     foreach ($doctor['pelayanan'] as $key => $pelayanan) {
        //         var_dump($pelayanan);
        //         echo "<br>";
        //     }
        // }
        echo count($doctor['pelayanan']);
        foreach ($doctor['pelayanan'] as $key => $value) {
            echo $key;
            echo "<br>";
            foreach ($value as $detail) {
                echo $detail;
            }
            echo "<hr>";
        }
    }
    // var_dump($doctor['pelayanan']);


    public function test()
    {
        $hari = "Kamis";

        //echo $dt;

        // $nama = $request->nama;
        //
        // $jadwal_hari = $request->jadwal_hari;
        // $lokasi = $request->lokasi;
        // $jenis_kelamin = $request->jenis_kelamin;
        // $spesialisasi = $request->spesialisasi;

        //	$data = DB::collection('faskes')->where('jam.hari','Selasa')->paginate(10);


        $dt = Carbon::now()->format('H:i');
        $data = DB::collection('dokter')
        ->when($dt, function ($query, $dt) {
            return $query->whereIn('jadwal.jadwal.mulai', '>=', ['00:00']);

            return $query->whereIn('jadwal.jadwal.mulai', ['00:00']);
        })

        // ->when($dt, function($query,$dt){
        //   return $query->whereIn('jadwal.jadwal.jam.selesai','<=',$dt);
        // })
        //
        // ->when($hari, function($query,$hari){
        //   return $query->where('jadwal.jadwal.hari',$hari);
        // })

        //->leftjoin('faskes','dokter.jadwal.faskes_kode','=','faskes.kode_rs')


          //where('nama','like','%'.$lokasi.'%')
      //   ->when($nama, function($query, $nama) {
       //   return $query->where('nama','like','%'.$nama.'%');
       // })
       //
       // ->when($jadwal_hari, function($query,$jadwal_hari){
       //   return $query->where('jam.hari',$jadwal_hari);
       // })
       //
       // ->when($lokasi, function($query,$lokasi){
       //   return $query->where('kota','like','%'.$lokasi.'%');
       // } )
       //
       // ->when($jenis_kelamin, function($query,$jenis_kelamin){
       //   return $query->where('jenis_kelamin','like','%'.$jenis_kelamin.'%');
       // } )
       //
            // ->when($spesialisasi, function ($query, $spesialisasi) {
       //     return $query->where('spesialis','like','%'.$spesialisasi.'%');
            //  })

        ->paginate(10);

        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully get hospital',

            ]);
    }

    public function faskes()
    {
        $id = "5a9586d2cac2167410a85144";

        $hehe = Doctor::where('_id', $id)->select('jadwal.faskes_kode')->get();
        $data = Hospital::where('jadwal.faskes');

        $decoded = json_encode($data);
        echo $decoded;
        // return response()->json([
            // 	'status'  => 200,
            // 	'data'    => $hehe,
            // 	'message' => 'Successfully get hospital',
        //
            // ]);
    }
}
