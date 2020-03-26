<?php

namespace App\Http\Controllers\Search;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Hospital;
use DB;
use Carbon\Carbon;

class SearchHospitalController extends Controller
{
    //mencari berdasarkan nama rumah sakit atau jenis pelayanan

    public function search_faskes(Request $request)
    {
        $nama = $request->input('nama');
        $asuransi = $request->input('asuransi');
        $jadwal_hari = $request->input('jadwal_hari');
        $lokasi = $request->input('lokasi');
        $buka = $request->input('buka');
        $jenis_faskes = $request->input('jenis_faskes');
        $jenis = $request->input('jenis');

        $data = DB::collection('faskes')

            ->when($asuransi, function ($query, $asuransi) {
                return $query->where('asuransi', $asuransi);
            })

             ->when($nama, function ($query, $nama) {
                 return $query->where('nama', 'like', '%'.$nama.'%');
             })
            //
            ->when($jadwal_hari, function ($query, $jadwal_hari) {
                return $query->where('jam.hari', $jadwal_hari);
            })
            //
            ->when($lokasi, function ($query, $lokasi) {
                return $query->where('kota', 'like', '%'.$lokasi.'%');
            })

            ->when($jenis_faskes, function ($query, $jenis_faskes) {
                return $query->where('jenis_faskes', $jenis_faskes);
            })

            ->when($jenis, function ($query, $jenis) {
                return $query->where('jenis', 'like', '%'.$jenis.'%');
            })

            ->where('show', true)
            ->orderBy('nama','asc')


            ->paginate(10);

        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully',

            ]);
    }

    public function suggestion(Request $request)
    {
        $lokasi = $request->input('lokasi');
        $data = Hospital::where('kota', 'like', '%'.$lokasi.'%')->distinct('kota')->get();
        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'good luck',

            ]);
    }

    public function dokter(Request $request)
    {
        //echo $id_faskes;
        $faskes_kode = $request->faskes_kode;

        $data = DB::collection('dokter')->where('jadwal.faskes_kode', $faskes_kode)

            ->paginate(10);
        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'good luck',

            ]);
    }

    public function faskes_jenis(Request $request)
    {
        $jenis_faskes = $request->input('jenis_faskes');

        $data = Hospital::where('jenis_faskes', $jenis_faskes)->get();
        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'good luck',

            ]);
    }

    public function faskes_jenis_tipe(Request $request)
    {
        $jenis_faskes = $request->input('jenis_faskes');
        $jenis = $request->input('jenis');


        $data = Hospital::where('jenis_faskes', $jenis_faskes)->where('jenis', 'like', $jenis)->get();
        return response()->json([
                'status'  => 200,
                'data'    => $data,
                'message' => 'good luck',

            ]);
    }

    public function nearest(Request $request)
    {
        $this->validate($request, [
                'longitude' => 'required|numeric',
                'latitude' => 'required|numeric',
            ]);



        $long = $request->input('longitude');
        $longitude = number_format($long, 2);
        $limit = $request->input('limit');

        $lat = $request->input('latitude');
        $latitude = number_format($lat, 6);



        // $hospital = DB::collection('test')->where('coordinates', 'near', [
        // 	'$geometry' => [
        // 		'type' => 'Point',
        // 		'coordinates' => [
        // 			$long,
        // 			$lat,
        // 		],
        // 	],
        // 	'$maxDistance' => 10000000,
        // ])->get();

        $data = DB::connection('mongodb')->command([
                'geoNear' => 'faskes',
                'near' => [$long,$lat],
                'limit' => $limit
            ])->toArray();

        return response()->json([
                'status'  => 200,
                'data'    => $data,

            ]);
    }

    public function nearestRS(Request $request)
    {
        $this->validate($request, [
                'longitude' => 'required|numeric',
                'latitude' => 'required|numeric',
            ]);



        $long = $request->input('longitude');
        $longitude = number_format($long, 2);
        $limit = $request->input('limit');

        $lat = $request->input('latitude');
        $latitude = number_format($lat, 6);

        $data = Hospital::where('coordinates','near',[
									'$geometry' => [
        					'type' => 'Point',
	    								'coordinates' => [
	        							$long,
												$lat
							        ],

								  ]

								]
							)->take($limit)
							
							->get();

        return response()->json([
                'status'  => 200,
                'data'    => $data,

            ]);
    }
}
