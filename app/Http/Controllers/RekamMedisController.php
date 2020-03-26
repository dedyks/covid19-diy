<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekamMedisController extends Controller
{
  public function __construct()
  {

  }

  public function create(Request $request){
    $id = $request->input('id');
    $decoded = $request->except(['id']);


    $decoded['id_pemeriksaan'] = hash('md5',Carbon::now()->format('Y-m-d h:m'));
    $decoded['datetime'] = Carbon::now()->format('Y-m-d h:m');

    DB::collection('rekammedis')->where('_id',$id)->push(
    ['pemeriksaan' => $decoded]
    );

    return response()->json([
      'status'  => 200,
      'data'    => $decoded,
      'message' => 'Successfully get doctor'
    ]);
  }


  public function view(Request $request){
    $email = $request->input('user_email');
    $data = DB::collection('rekammedis')->where('user_email',$email)->first();
    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'message' => 'Successfully'
    ]);
  }


}
