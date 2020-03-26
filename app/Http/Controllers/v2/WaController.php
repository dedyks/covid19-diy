<?php

namespace App\Http\Controllers\v2;


class WaController extends Controller
{
  public function __construct()
  {

  }

  public function bookFaskes(Request $request){
    $this->validate($request, [
    'user_id' => 'required',

]);

    $book = new Book();
    $book->faskes_id =  $request->input('faskes_id');
    $book->dokter_id = null;
    $book->user_id = $request->input('user_id');
    $book->tgl_reservasi = $request->input('tgl_reservasi');
    $book->keterangan = $request->input('keterangan');
    $book->book_status = "BOOKED";
    $book->pembayaran = $request->input('pembayaran');
    $book->fcm_token = $request->input('fcm_token');
    $book->status_infomedis = $request->input('status_infomedis');
    $book->status_pasien = $request->input('status_pasien');
    $book->nomor_rekammedis = $request->input('nomor_rekammedis');
    $book->keluhan = $request->input('keluhan');
    $book->riwayat_alergi = $request->input('riwayat_alergi');
    $book->riwayat_penyakit = $request->input('riwayat_penyakit');
    $book->save();

    $latestBook = Book::where('faskes_id',$request->input('faskes_id'))->where('user_id',$request->input('user_id'))
    ->with('user:id,name,jenisKelamin,telp,alamat,pekerjaan,pendidikanTerakhir','hospital:id,nama,email')
    ->first();

    $tempat = $latestBook->hospital;
    $waktu = $latestBook->tgl_reservasi;
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60*20);
    $option = $optionBuilder->build();

    //notif
    $notificationBuilder = new PayloadNotificationBuilder('status anda book');
    $notificationBuilder->setBody('di '.$tempat->nama.' pukul '.$waktu.'')
                ->setSound('default');
    $notification = $notificationBuilder->build();


    //data
    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['Data' => $latestBook]);
    $data = $dataBuilder->build();


    $token = $request->input('fcm_token');
    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();

    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();

    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();

    //mengirim Email
    $dt = Carbon::now()->toFormattedDateString();
    $nama_maps = urlencode($tempat->nama);
    $gmaps = "http://www.google.com/maps/search/?api=1&query=".$nama_maps."";
    $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
    $domain = "mg.medup.id";

    $view = view('new_book',['date' => $dt,
    'data' => $latestBook,
    'book' => $book,
    ]);
# Make the call to the client.


    try {
        $result = $mgClient->sendMessage($domain,
            array('from' => 'Medup Blast <postmaster@mg.medup.id>',
                'to' => $latestBook->hospital->email,
                'subject' => 'Hello '.$latestBook->hospital->nama.'',
                'html' => $view));
    } catch (MissingRequiredMIMEParameters $e) {
    }

    return response()->json([
      'status'  => 200,
      'data'    => $latestBook->hospital->email,
      'message' => $result,
      'status' => 'ok'
    ]);
  }

  public function book(Request $request){
    $this->validate($request, [
    'user_id' => 'required',

      ]);

    $book = new Book();
    $book->dokter_id = $request->input('dokter_id');
    $book->faskes_id =  $request->input('faskes_id');
    $book->user_id = $request->input('user_id');

    $book->tgl_reservasi = $request->input('tgl_reservasi');
    $book->keterangan = $request->input('keterangan');
    $book->book_status = "BOOKED";
    $book->asuransi = $request->input('asuransi');
    $book->nomor_asuransi = $request->input('nomor_asuransi');
    $book->fcm_token = $request->input('fcm_token');
    $book->nomorRM = $request->input('nomorRM');
    $book->save();

    $latestBook = Book::where('dokter_id',$request->input('dokter_id'))->where('faskes_id',$request->input('faskes_id'))->where('user_id',$request->input('user_id'))->where('tgl_reservasi',$request->input('tgl_reservasi'))
    ->with('user:id,name,jenisKelamin,telp,email','doctor:id,nama','hospital:id,nama')
    ->first();
    $tempat = $latestBook->hospital;
    $waktu = $latestBook->tgl_reservasi;
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60*20);
    $option = $optionBuilder->build();

    //notif
    $notificationBuilder = new PayloadNotificationBuilder('status anda book');
    $notificationBuilder->setBody('di '.$tempat->nama.' pukul '.$waktu.'')
                ->setSound('default');
    $notification = $notificationBuilder->build();


    //data
    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['Data' => $latestBook]);
    $data = $dataBuilder->build();


    $token = $request->input('fcm_token');
    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();

    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();

    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();



      //mengirim Email
        $nama_maps = urlencode($tempat->nama);
        $gmaps = "http://www.google.com/maps/search/?api=1&query=".$nama_maps."";
        $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
        $domain = "mg.medup.id";
        $dt = Carbon::now()->toFormattedDateString();

    $view = view('new_book',['date' => $dt,
    'data' => $latestBook,
    'book' => $book,
    ]);
# Make the call to the client.


        try {
            $result = $mgClient->sendMessage($domain,
                array('from' => 'MedUp <postmaster@mg.medup.id>',
                    'to' => 'Dedy Kurniawan Santoso <dedysmd@hotmail.com>',
                    'subject' => 'Hello Dedy Kurniawan Santoso',
                    'html' => $view));
        } catch (MissingRequiredMIMEParameters $e) {
        }




    return response()->json([
      'status'  => 200,
      'data'    => $latestBook,
      'message' => $result,
      'ok to' => 'ok'
    ]);
  }

  public function bookKeluarga(Request $request){
      $user = new User();
      $user->email = null;
      $user->password = null;
      $user->name = $request->input('name');
      $user->role = 'pasien';
      $user->tglLahir = $request->input('tglLahir');
      $user->jenisKelamin = $request->input('jenisKelamin');
      $user->telp = $request->input('telp');
      $user->statusNikah = null;
      $user->no_KTP = $request->input('no_ktp');
      $user->pendidikanTerakhir = null;
      $user->pekerjaan = null;
      $user->telp = null;
      $user->kesehatan = array("golDarah" => null,
          "rhDarah" => null,
          "berat" => null,
          "tinggi" => null,
          "alergi" => array(),
          "penyakit" => array(),
          "merokok" => null,

      );
      $user->nomorRM = $request->input('nomorRM');
      $user->asuransi = $request->input('asuransi');
      $user->favorit = array();
      $user->hubungan = $request->input('hubungan');
      $user->id_hubungan = $request->input('user_id');

      $user->save();

      $book = new Book();
      $book->dokter_id = $request->input('dokter_id');
      $book->faskes_id =  $request->input('faskes_id');
      $book->user_id = $request->input('user_id');

      $book->tgl_reservasi = $request->input('tgl_reservasi');
      $book->keterangan = $request->input('keterangan');
      $book->book_status = "BOOKED";
      $book->fcm_token = $request->input('fcm_token');
      $book->save();

      $latestBook = Book::where([
    ['dokter_id', $request->input('dokter_id')],
    ['faskes_id', $request->input('faskes_id')],
    ['user_id', $request->input('user_id')],
])
      ->with('user:id,name,jenisKelamin,telp','doctor:id,nama','hospital:id,nama')
      ->get();


      return response()->json([
          'status'  => 200,
          'data'    => $latestBook,
          'message' => 'Success'
      ]);


  }


  public function GetBookByFaskes(Request $request){
   $faskes = $request->input('faskes_id');
   $data = Book::where('faskes_id',$faskes)->with('user:id,name,jenisKelamin,telp','doctor:id,nama','hospital:id,nama')->get();

   return response()->json([
     'status'  => 200,
     'data'    => $data,
     'message' => 'Successfully'
   ]);

 }


  public function update($id,Request $request){
    $query = Book::find($id);
    //$data->tgl_reservasi = $request->input('tgl_reservasi');
    $query->book_status = $request->input('status');
    $query->save();
    $tempat = $query->hospital;
    $waktu = $query->tgl_reservasi;

    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60*20);
    $option = $optionBuilder->build();

    //notif
    $notificationBuilder = new PayloadNotificationBuilder('status anda '.$query->book_status.'');
    $notificationBuilder->setBody('di '.$tempat->nama.' pukul '.$waktu.'')
                ->setSound('default');
    $notification = $notificationBuilder->build();


    //data
    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['Data' => $query]);
    $data = $dataBuilder->build();


    $token = $query->fcm_token;
    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();

    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();

    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();


    return response()->json([
      'status'  => 200,
      'data'    => $query,
      'message' => 'Successfully'
    ]);

  }



  public function antrian(Request $request){
    $user_id = $request->input('user_id');
    $faskes_id = $request->input('faskes_id');
    $sesi_start = $request->input('sesi_start');
    $dokter_id = $request->input('dokter_id');
    $sesi_end = $request->input('sesi_end');
    $date = $request->input('date');
   
         $dataAntrianUser = AntrianUser::where([
           ['user_id','=',$user_id],
           ['faskes_id','=',$faskes_id],
           ['dokter_id','=',$dokter_id],
           ['sesi_start','=',$sesi_start],
           ['sesi_end','=',$sesi_end],
           ['date','=',$date],
         ])->get();

         $dataAntrianFaskes = AntrianFaskes::where([
          ['faskes_id','=',$faskes_id],
          ['dokter_id','=',$dokter_id],
          ['sesi_start','=',$sesi_start],
          ['sesi_end','=',$sesi_end],
          ['date','=',$date]
        ])->get();
         
            return response()->json([
              'status'  => 200,
              'antrian Faskes' => $dataAntrianFaskes,
              'antrian User' => $dataAntrianUser
            ]);
  }

  public function antrianAdd(Request $request){
    $user_id = $request->input('user_id');
    $faskes_id = $request->input('faskes_id');
    $sesi_start = $request->input('sesi_start');
    $dokter_id = $request->input('dokter_id');
    $sesi_end = $request->input('sesi_end');
    $date = $request->input('date');
  
         
         $dataAntrianFaskes = AntrianFaskes::where([
          ['faskes_id','=',$faskes_id],
          ['dokter_id','=',$dokter_id],
          ['sesi_start','=',$sesi_start],
          ['sesi_end','=',$sesi_end],
          ['date','=',$date]
        ])->first();

        $dataAntrianFaskes->increment('antrian_total');
        $dataAntrianFaskes->save(); 
         

        $dataAntrianUser = new AntrianUser();
        $dataAntrianUser->user_id = $user_id;
        $dataAntrianUser->faskes_id = $faskes_id;
        $dataAntrianUser->dokter_id = $dokter_id;
        $dataAntrianUser->sesi_start = $sesi_start;
        $dataAntrianUser->sesi_end = $sesi_end;
        $dataAntrianUser->date = $date;
        $dataAntrianUser->no_antrian = $dataAntrianFaskes->antrian_total;
        $dataAntrianUser->save();
        
            return response()->json([
              'status'  => 200,
              'antrian Faskes' => $dataAntrianFaskes,
              'antrian User' => $dataAntrianUser
            ]);
  }

  public function antrianReset(Request $request){
    
    $faskes_id = $request->input('faskes_id');
    $sesi_start = $request->input('sesi_start');
    $dokter_id = $request->input('dokter_id');
    $sesi_end = $request->input('sesi_end');
    $date = $request->input('date');
    $antrian_sekarang = $request->input('antrian_sekarang');
    $antrian_total = $request->input('antrian_total');
  
         
         $dataAntrianFaskes = AntrianFaskes::where([
          ['faskes_id','=',$faskes_id],
          ['dokter_id','=',$dokter_id],
          ['sesi_start','=',$sesi_start],
          ['sesi_end','=',$sesi_end],
          ['date','=',$date]
        ])->first();

        $dataAntrianFaskes->antrian_total = $antrian_total;
        $dataAntrianFaskes->antrian_sekarang = $antrian_sekarang;
        $dataAntrianFaskes->save(); 
        
        
            return response()->json([
              'status'  => 200,
              'antrian Faskes' => $dataAntrianFaskes,
            
            ]);
  }

  public function antrianFaskesDokterSesi(Request $request){
    
    $faskes_id = $request->input('faskes_id');
    $sesi_start = $request->input('sesi_start');
    $dokter_id = $request->input('dokter_id');
    $sesi_end = $request->input('sesi_end');
    $date = $request->input('date');
    $antrian_sekarang = $request->input('antrian_sekarang');
    $antrian_total = $request->input('antrian_total');
  
         
         $dataAntrianFaskes = AntrianFaskes::where([
          ['faskes_id','=',$faskes_id],
          ['dokter_id','=',$dokter_id],
          ['sesi_start','=',$sesi_start],
          ['sesi_end','=',$sesi_end],
          ['date','=',$date]
        ])->get();

        $dataAntrianUser = AntrianUser::where([
          ['faskes_id','=',$faskes_id],
          ['dokter_id','=',$dokter_id],
          ['sesi_start','=',$sesi_start],
          ['sesi_end','=',$sesi_end],
          ['date','=',$date]
        ])->orderBy('no_antrian','desc')->get();

        
            return response()->json([
              'status'  => 200,
              'antrian Faskes' => $dataAntrianFaskes,
              'antrian User' => $dataAntrianUser
            ]);
  }

  public function CancelBooking(Request $request){
    $id = $request->input('booking_id');
    $data = Book::find($id);

    //$dt = Carbon::now('Asia/Bangkok');
    $data->queue_time = NULL;
    $data->book_status = 'CANCELLED';
    $data->save();

    return response()->json([
      'status'  => 200,
      'data'    => $book,

      'message' => 'Successfully'
    ]);
  }

  public function blastEmail(Request $request){
    $this->validate($request, [
      'user_id' => 'required',
  
        ]);
     
      $faskesData = Hospital::find($request->input('faskes_id'));
      
      if($faskesData){
        $check = $faskesData->partnered;
        if($check==="TRUE")
        {
          $book = new Book();
          $book->dokter_id = $request->input('dokter_id');
          $book->faskes_id =  $request->input('faskes_id');
          $book->user_id = $request->input('user_id');
      
          $book->tgl_reservasi = $request->input('tgl_reservasi');
          $book->tgl_reservasi_sesi_start = $request->input('tgl_reservasi_sesi_start');
          $book->tgl_reservasi_sesi_end = $request->input('tgl_reservasi_sesi_end');
    
    
          $book->keterangan = $request->input('keterangan');
          $book->book_status = "BOOKED";
          $book->asuransi = $request->input('asuransi');
          $book->merokok = $request->input('merokok');
          $book->pembayaran = $request->input('pembayaran');
    
    
          $book->save();
          
          $latestBook = Book::where('dokter_id',$request->input('dokter_id'))->where('faskes_id',$request->input('faskes_id'))->where('user_id',$request->input('user_id'))->where('tgl_reservasi',$request->input('tgl_reservasi'))
          ->with('user:id,firstName,lastName,jenisKelamin,telp,email','doctor:id,nama,gelar_depan','hospital:id,nama,email,partnered')
          ->first();
  
          $email = $latestBook->hospital->email;
       
                //mengirim Email
                
              
                $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
                $domain = "mg.medup.id";
                $dt = Carbon::now()->toFormattedDateString();
    
            $view = view('new_book',['date' => $dt,
            'data' => $latestBook
            ])->render();
          # Make the call to the client.
    
    
                try {
                  $result = $mgClient->sendMessage($domain,
                  array('from' => 'MedUp <postmaster@mg.medup.id>',
                      'to' => $email,
                      'subject' => 'Booking Pasien '.$latestBook->tgl_reservasi.'',
                      'html' => $view));
                } catch (MissingRequiredMIMEParameters $e) {
                }
    
    
    
    
            return response()->json([
              'status'  => 200,
              'data'    => $latestBook,
              'message' => $result,
    
            ]);
        }
        else
        {
          return response()->json([
            'status'  => 202,
            'message' => "tidak partnered",
          ],202);
        }
      }
      else
      {
        return response()->json([
          'status'  => 202,
          'message' => "faskes_id tidak terdaftar",
        ],202);
      }
      
     


  }

  public function cancelBlastEmail(Request $request){
   
    $id=$request->book_id;
    $latestBook = Book::find($id)->with('user:id,firstName,lastName,jenisKelamin,telp,email','doctor:id,nama,gelar_depan','hospital:id,nama,email,partnered')
    ->first();
  
    $latestBook->book_status = "CANCELLED";
   
    if($latestBook)
    {
      $check = $latestBook->hospital->partnered;
      $email = $latestBook->hospital->email;
      if($check==="TRUE")
      {
             
              //mengirim Email
              
            
              $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
              $domain = "mg.medup.id";
              $dt = Carbon::now()->toFormattedDateString();
  
          $view = view('cancel_email',['date' => $dt,
          'data' => $latestBook
          ])->render();
        # Make the call to the client.
  
  
              try {
                  $result = $mgClient->sendMessage($domain,
                      array('from' => 'MedUp <postmaster@mg.medup.id>',
                          'to' => $email,
                          'subject' => 'Batal Pasien '.$latestBook->tgl_reservasi.'',
                          'html' => $view));
              } catch (MissingRequiredMIMEParameters $e) {
              }
  
  
  
  
          return response()->json([
            'status'  => 200,
            'data'    => $latestBook,
            'message' => $result,
  
          ]);
      }
      else
      {
        return response()->json([
          'status'  => 202,
          'message' => "tidak partnered"
        ]);
      }
    }
    else
      {
        return response()->json([
          'status'  => 202,
          'message' => "book_id tidak ditemukan"
        ]);
      }
    
    
      
  }

}
