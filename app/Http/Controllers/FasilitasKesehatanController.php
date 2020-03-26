<?php

namespace App\Http\Controllers;
use DB;
use App\Hospital;
use Illuminate\Http\Request;

class FasilitasKesehatanController extends Controller
{
  public function __construct()
  {

  }

  public function getAll(){
    $data=DB::collection('fasilitas_faskes')
        ->paginate(10);
    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'message' => 'Successfully get Fasilitas Kesehatan'
    ]);
  }

  public function asuransiFaskes(Request $request){
    $id = $request->input('faskes_id');
    $data = Hospital::where('_id',$id)->select('asuransi')->get();

    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'message' => 'Successfully'
    ]);
  }



}
