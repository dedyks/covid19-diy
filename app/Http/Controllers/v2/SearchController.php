<?php

namespace App\Http\Controllers\v2;

use Illuminate\Http\Request;
use App\WilayahIndonesia;
use DB;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    public function getSpesialis()
    {
        $data = DB::collection('spesialis_dokter')->get();

        return response()->json([
                  'status' => 200,
                  'data' => $data,
                  'message' => 'Successful',
                ]);
    }

    public function suggestionWilayah(Request $request)
    {
        $nama = $request->input('nama');
        $limit = (int) $request->input('limit');
        $data = WilayahIndonesia::where('nama', 'like', '%'.$nama.'%')
                            ->whereRaw(array('$where' => 'this.kode.length == 5'))
                            ->limit($limit)
                            ->select('nama')
                            ->get();

        return response()->json([
          'status' => 200,
                'data' => $data,
                'limit' => $limit,
          'message' => 'Success',
        ]);
    }
}
