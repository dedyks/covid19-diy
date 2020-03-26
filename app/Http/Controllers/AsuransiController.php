<?php

namespace App\Http\Controllers;
use DB;
use App\Hospital;
use Illuminate\Http\Request;

class AsuransiController extends Controller
{
  public function __construct()
  {

  }

  const MODEL = "App\Asuransi";

  use RESTActions;


  public function getAll(){
    $data=DB::collection('asuransi')
        ->paginate(10);
    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'message' => 'Successfully get Asuransi'
    ]);
  }




}
