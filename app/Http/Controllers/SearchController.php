<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Hospital;
use DB;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getSpesialis(){
      $data = DB::collection('spesialis_dokter')
              ->paginate(10);

              return response()->json([
            	  'status'  => 200,
            	  'data'    => $data,
            	  'message' => 'Successful'
            	]);
    }


    public function testget(){
    	$hospital = Hospital::all();
    	return response()->json([
    	  'status'  => 200,
    	  'data'    => $hospital,
    	  'message' => 'Successfully get hospital'
    	]);
    }

    public function searchService($service, $location){
    	$hospitals = Hospital::all();
		$rs = array();
		$service = strtolower($service);
		$service = str_replace("%20"," ",$service);

		foreach ($hospitals as $key => $hospital) {
			$hospital['pelayanan_umum'] = '';
			$hospital['pelayanan_unggulan'] = '';
			foreach ($hospital['pelayanan']['umum'] as $key => $value) {
				$hospital['pelayanan_umum'] .= $value['nama'].',';
			}
			foreach ($hospital['pelayanan']['unggulan'] as $key => $value) {
				$hospital['pelayanan_unggulan'] .= $value['nama'].',';
			}
			$pelayanan = $hospital['pelayanan_umum'].$hospital['pelayanan_unggulan'];
    		$pelayanan = strtolower($pelayanan);
    		$rumahsakit = $hospital['nama'];
    		$rumahsakit = strtolower($rumahsakit);
    		if ((strpos($pelayanan, $service) !== false) || (strpos($rumahsakit, $service) !== false)) {
				array_push($rs,$hospital['kode_rs']);
    		}
		}
		$hospitals = Hospital::whereIn('kode_rs',$rs)->get();
		return response()->json([
		  'status'  => 200,
		  'data'    => $hospitals,
		  'message' => 'Successfully get hospital'
		]);

    }

    public function getHospital($service, $location){
    	$service = strtolower($service);
    	$service = str_replace("%20"," ",$service);
    	$location = str_replace("%20"," ",$location);

    	if ($service == 'all' && $location == 'indonesia') {
    		$hospitals = Hospital::all();
    	}
    	elseif ($service == 'all') {
    		$hospitals = Hospital::where('kota','LIKE','%'.$location.'%')->get();
    	}
    	elseif ($location == 'indonesia') {
    		$hospitals = Hospital::all();
			$rs = array();
			foreach ($hospitals as $key => $hospital) {
						$hospital['pelayanan_umum'] = '';
						$hospital['pelayanan_unggulan'] = '';
						foreach ($hospital['pelayanan']['umum'] as $key => $value) {
							$hospital['pelayanan_umum'] .= $value['nama'].',';
						}
						foreach ($hospital['pelayanan']['unggulan'] as $key => $value) {
							$hospital['pelayanan_unggulan'] .= $value.',';
						}
						$pelayanan = $hospital['pelayanan_umum'].$hospital['pelayanan_unggulan'];
			    		$pelayanan = strtolower($pelayanan);
			    		$rumahsakit = $hospital['nama'];
			    		$rumahsakit = strtolower($rumahsakit);
			    		if ((strpos($pelayanan, $service) !== false) || (strpos($rumahsakit, $service) !== false)) {
							array_push($rs,$hospital['kode_rs']);
			    		}
					}
			$hospitals = Hospital::whereIn('kode_rs',$rs)->get();
    	}
    	else{
    		$hospitals = Hospital::where('kota','LIKE','%'.$location.'%')->get();
			$rs = array();
			foreach ($hospitals as $key => $hospital) {
						$hospital['pelayanan_umum'] = '';
						$hospital['pelayanan_unggulan'] = '';
						foreach ($hospital['pelayanan']['umum'] as $key => $value) {
							$hospital['pelayanan_umum'] .= $value['nama'].',';
						}
						foreach ($hospital['pelayanan']['unggulan'] as $key => $value) {
							$hospital['pelayanan_unggulan'] .= $value['nama'].',';
						}
						$pelayanan = $hospital['pelayanan_umum'].$hospital['pelayanan_unggulan'];
			    		$pelayanan = strtolower($pelayanan);
			    		$rumahsakit = $hospital['nama'];
			    		$rumahsakit = strtolower($rumahsakit);
			    		if ((strpos($pelayanan, $service) !== false) || (strpos($rumahsakit, $service) !== false)) {
							array_push($rs,$hospital['kode_rs']);
			    		}
					}
			$hospitals = Hospital::whereIn('kode_rs',$rs)->get();
    	}

    	return response()->json([
    	  'status'  => 200,
    	  'data'    => $hospitals,
    	  'message' => 'Successfully get hospital'
    	]);
    }
}
