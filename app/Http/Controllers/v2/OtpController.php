<?php

namespace App\Http\Controllers\v2;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Otp;
use Mailgun\Mailgun;


class OtpController extends Controller
{
  public function __construct()
  {

  }


  public function createTCASTSMS(Request $request) {
    $this->validate($request,[
      'no_telp' => 'required'
    ]);

    $telp = $request->input('no_telp');
    $digits = 5;
    $kodeINT = rand(pow(10, $digits-1), pow(10, $digits)-1);
    $kode = "$kodeINT";

    $data = new Otp();
    $data->kode_otp = $kode;
    $data->telp = $telp;
    $data->save();

    $req_url = 'http://login8.tcastsms.net/sendsms?username=acit-medup8&password=93318767&type=0&dlr=1&destination='.$telp.'&source=TCASTSMS&message='.'Angka konfirmasi Anda untuk Aplikasi MedUp adalah : '.$kode;
    $req_url = str_replace(" ","%20",$req_url);
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $req_url,
    ));
    $resp = curl_exec($curl);
    
    if (!$resp) {
        die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
    } else {
        header('Content-type: text/json'); /*if you want to output to be an xml*/
        echo $resp;
    }
    curl_close($curl);
  }

  public function createNusaSms(Request $request) {
    $this->validate($request,[
      'no_telp' => 'required'
    ]);

    $telp = $request->input('no_telp');
    $digits = 5;
    $kodeINT = rand(pow(10, $digits-1), pow(10, $digits)-1);
    $kode = "$kodeINT";

    $data = new Otp();
    $data->kode_otp = $kode;
    $data->telp = $telp;
    $data->save();

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => 'http://api.nusasms.com/api/v3/sendsms/plain',
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => array(
        'user' => 'medup_api',
        'password' => 'u17drM5',
        'SMSText' => 'Hai,\nKami dari tim MedUp, Kode verifikasi  anda adalah '.$kode,
        'GSM' => $telp,
        'output' => 'json'
      )
    ));

    $resp = curl_exec($curl);

    if (!$resp) {
        die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
    } else {
        header('Content-type: text/json'); /*if you want to output to be an xml*/
        echo $resp;
    }
    curl_close($curl);

  }

  public function createEmail(Request $request)
  {
    $this->validate($request,[
      'email' => 'required|email',
      'no_telp' => 'required'
    ]);

    $email = $request->input('email');
    $telp = $request->input('no_telp');
    $digits = 5;
    $kodeINT = rand(pow(10, $digits-1), pow(10, $digits)-1);
    $kode = "$kodeINT";

    $data = new Otp();
    $data->kode_otp = $kode;
    $data->telp = $telp;
    $data->save();
    //mengirim Email
    $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
    $domain = "mg.medup.id";
    $dt = Carbon::now()->toFormattedDateString();

$view = view('EmailOTP',['date' => $dt,
'kode' => $kode
]);
# Make the call to the client.


    try {
        $result = $mgClient->sendMessage($domain,
            array('from' => 'MedUp <postmaster@mg.medup.id>',
                'to' => $email,
                'subject' => 'kode Verifikasi Medup Anda',
                'html' => $view));
    } catch (MissingRequiredMIMEParameters $e) {
    }




return response()->json([
  'status'  => 201,
  'message' => $result,
  'ok to' => 'ok'
]);
  }

  public function create(Request $request){
    $this->validate($request,[
            'no_telp' => 'required'
            ]);
    $telp = $request->input('no_telp');
    //mengambil 10 digit +62xxx. 08xxx
    $newTelp = substr($telp, -10);

    $digits = 5;
    $kodeINT = rand(pow(10, $digits-1), pow(10, $digits)-1);
    $kode = "$kodeINT";
    $data = new Otp();
    $data->kode_otp = $kode;
    $data->telp = $newTelp;
    $data->save();

    ob_start();
// setting
$apikey      = '757f2a2eae19e8ae5aa59afd2c824350'; // api key
$urlserver   = 'http://45.76.156.114/sms/api_sms_otp_send_json.php'; // url server sms
$callbackurl = ''; // url callback get status sms
$senderid    = '0'; // Option senderid 0=Sms Long Number / 1=Sms Masking/Custome Senderid

// create header json
$senddata = array(
	'apikey' => $apikey,
	'callbackurl' => $callbackurl,
	'senderid' => $senderid,
	'datapacket'=>array()
);

// create detail data json
// data 1
$number=$telp;
$message="Your Medup verification code is M-" .$kode;
array_push($senddata['datapacket'],array(
	'number' => trim($number),
	'message' => $message
));
// sending
$data=json_encode($senddata);
$curlHandle = curl_init($urlserver);
curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data))
);
curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);
$respon = curl_exec($curlHandle);

$curl_errno = curl_errno($curlHandle);
$curl_error = curl_error($curlHandle);
$http_code  = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
curl_close($curlHandle);
if ($curl_errno > 0) {
	$senddatax = array(
	'sending_respon'=>array(
		'globalstatus' => 90,
		'globalstatustext' => $curl_errno."|".$http_code)
	);
	$respon=json_encode($senddatax);
} else {
	if ($http_code<>"200") {
		$senddatax = array(
		'sending_respon'=>array(
			'globalstatus' => 90,
			'globalstatustext' => $curl_errno."|".$http_code)
		);
		$respon= json_encode($senddatax);
	}
}
header('Content-Type: application/json');
echo $respon;




  }

  public function balance(){
    ob_start();
    // setting
    $apikey      = '757f2a2eae19e8ae5aa59afd2c824350'; // api key
    $urlserver   = 'http://45.76.156.114/sms/api_sms_otp_send_json.php'; // url server sms

    // create header json
    $senddata = array(
    	'apikey' => $apikey
    );

    // sending
    $data=json_encode($senddata);
    $curlHandle = curl_init($urlserver);
    curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
    			'Content-Type: application/json',
    			'Content-Length: ' . strlen($data))
    );
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 5);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);
    $respon = curl_exec($curlHandle);

    $curl_errno = curl_errno($curlHandle);
    $curl_error = curl_error($curlHandle);
    $http_code  = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
    curl_close($curlHandle);
    if ($curl_errno > 0) {
    	$senddatax = array(
    	'sending_respon'=>array(
    		'globalstatus' => 90,
    		'globalstatustext' => $curl_errno."|".$http_code)
    	);
    	$respon=json_encode($senddatax);
    } else {
    	if ($http_code<>"200") {
    		$senddatax = array(
    		'sending_respon'=>array(
    			'globalstatus' => 90,
    			'globalstatustext' => $curl_errno."|".$http_code)
    		);
    		$respon= json_encode($senddatax);
    	}
    }
    header('Content-Type: application/json');
    echo $respon;
  }

  public function status(){
    // sample get status sms
    // upload to your server
    $respondata=json_decode(file_get_contents('php://input'),TRUE);
    if (!empty($respondata))
    {
    	foreach($respondata['status_respon'] as $data)
    		{
    			$sendingid          = $data['sendingid'];
    			$number             = $data['number'];
    			$deliverystatus     = $data['deliverystatus'];
    			$deliverystatustext = $data['deliverystatustext'];

    			// here ..... insert/update ....sql....table

    		}
    }
  }
 



  public function checkKode(Request $request){
    $kode = $request->input('kode');
    $telp = $request->input('no_telp');
   
    $kode = strval($kode);
    $newTelp = substr($telp, -10);

    $check = DB::collection('otp')->where('kode_otp',$kode)->where('telp','like','%'.$newTelp)->count();


    if ($check>=1) {
      // code...
      return response()->json([
        'status'  => 200,
        'message' => TRUE
        ]);
    }
    else{
      return response()->json([
        'status'  => 200,
        'message' => FALSE
        ]);
    }
    }

  }
