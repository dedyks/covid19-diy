<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ScreeningController extends Controller
{
    const MODEL = "App\Screening";

    use RESTActions;

    public function submit(Request $request)
    {
        $m = self::MODEL;
        $this->validate($request, $m::$rules);
        $input_data = $request->all();
        $counter_riwayat = (int) $request->input('counter_riwayat');
        $counter_gejala = (int) $request->input('counter_gejala');

        if ($counter_riwayat == 0) {
            $input_data['kesimpulan'] = 'Triase';
        } elseif ($counter_riwayat >= 0 && $counter_gejala == 0) {
            // code...
            $input_data['kesimpulan'] = 'Ruang periksa 2';
        } elseif ($counter_riwayat >= 0 && $counter_gejala > 0) {
            $input_data['kesimpulan'] = 'Ruang periksa 1';
        }

        return $this->respond(Response::HTTP_CREATED, $m::create($input_data));
    }

    public function UiiSubmit(Request $request)
    {
        $m = self::MODEL;
        $this->validate($request, $m::$rules);
        $input_data = $request->all();
        $counter_gejala = (int) $request->input('counter_gejala');
        $counter_faktor_resiko = (int) $request->input('counter_faktor_resiko');

        if ($counter_gejala > 0 || $counter_faktor_resiko > 0) {
            $input_data['kesimpulan'] = 'Poli Skrining';
        } else {
            $input_data['kesimpulan'] = 'Physical Distancing dan jaga pola hidup bersih sehat';
        }

        return $this->respond(Response::HTTP_CREATED, $m::create($input_data));
    }

    public function getUii()
    {
        $m = self::MODEL;

        return $this->respond(Response::HTTP_OK, $m::where('id_faskes', 'rsuii')->get());
    }
}
