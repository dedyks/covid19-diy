<?php

namespace App\Http\Controllers;
use DB;
use App\Book;
use App\User;
use App\Doctor;
use App\Hospital;
use Carbon\Carbon;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Mailgun\Mailgun;
use Mailgun\Messages\Exceptions\MissingRequiredMIMEParameters;
use Illuminate\Http\Request;

class BookController extends Controller
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
    $book->fcm_token = $request->input('fcm_token');
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
    $data = Book::where('user_email',$request->input('user_email'))
            ->orderBy('queue_time.date','asc')->get();

            // where('dokter_id',$request->input('dokter_id'))
            //         ->where('faskes_id',$request->input('faskes_id'))
            //         ->
            return response()->json([
              'status'  => 200,
              'data'    => $data,

              'message' => 'Successfully'
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


  public function test(Request $request){
    $this->validate($request, [
      'user_id' => 'required',
  
        ]);
      $email = $request->input('email_tujuan_test');
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
                      'to' => $email,
                      'subject' => 'Hello '.$latestBook->hospital->nama.'',
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

}