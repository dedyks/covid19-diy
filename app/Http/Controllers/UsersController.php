<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\ResetPassword;
use App\RekamMedis;
use App\Hospital;
use App\Doctor;
use Mailgun\Mailgun;
use Mailgun\Messages\Exceptions\MissingRequiredMIMEParameters;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    const MODEL = "App\User";

    use RESTActions;

    public function store(Request $request)
    {
        $user = new User;
        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        return $user;
    }

    public function loginGanti(Request $request)
    {
        $email=$request->input('email');
        $password=$request->input('password');

        $hasher = app()->make('hash');

        //  $login = User::where('email', $email)->first();
        $login = User::where('email', $email);

        if ($login==0) {
            $res['success'] = false;
            $res['message'] = 'Your email or password incorrect!';
            return response($res);
        } else {
            $api_token = sha1(time());
            $create_token = User::where('_id', $login->id)->update(['api_token' => $api_token]);
            if ($create_token) {
                $res['success'] = true;
                $res['api_token'] = $api_token;
                $res['message'] = $login;
                return response($res);
            }
        }
    }

    public function login(Request $request)
    {
       
        $email = $request->input('email');
        $salt = $dataUser->salt;
        $password_input = $request->input('password');
            $salted_password = $password_input.$salt;
            $password_secure = hash('sha256', $salted_password);
       
        $check = User::where('email', '=', $email)->count();
        $dataUser = User::where('email',$email)->get();
        if ($check==0) {
            $res['success'] = true;
            //$res['api_token'] = $api_token;
            $res['message'] = 'Email dan password salah';
            return response($res);
        } elseif ($check > 0) {
            $api_token = sha1(time());
            $data = User::where('email', '=', $request->input('email'))->where('password',$password_secure)->first();
           
            if ($data['role'] == 'admin') {
                $res['success'] = true;
                $res['api_token'] = $api_token;
                $res['data'] = $data;
                $res['message'] = 'Bener dan Admin';
                return response($res);
            } elseif ($data['role'] == 'pasien') {
                $res['success'] = true;
                $res['api_token'] = $api_token;
                $res['data'] = $data;
                $res['message'] = 'Bener dan pasien';
                return response($res);
            } elseif ($data['role'] == 'pasien') {
                $res['success'] = true;
                $res['api_token'] = $api_token;
                $res['data'] = $data;
                $res['message'] = 'Bener dan pasien';
                return response($res);
            }
        }
    }

    public function profileEdit(Request $request)
    {
        $data = User::find($request->input('id'));
        $data->name = $request->input('name');
        $data->tempatLahir = $request->input('tempatLahir');
        $data->statusNikah = $request->input('statusNikah');
        $data->pekerjaan = $request->input('pekerjaan');
        $data->pendidikanTerakhir = $request->input('pendidikanTerakhir');
        $data->alamat = $request->input('alamat');
        $data->save();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Successfully get profile']);
    }


    public function profile(Request $request)
    {
        $id = $request->input('id');
        $data = User::where('_id', $id)->get();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Successfully get profile']);
    }

    public function registerValidate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users'
        ]);
        return response()->json([
            'status'  => 200,
            'message' => 'data validated']);
    }

    public function register(Request $request)
    {
        $inputEmail = $request->email;
        $check = User::where('email', $inputEmail);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);


        if ($request->input('role')=='pasien') {
            $rekammedis = new RekamMedis();
            $rekammedis->user_email = $request->email;
            $rekammedis->save();
        } elseif ($request->input('role')=='dokter') {
            $dokter = new Doctor();
            $dokter->nama = $request->input('nama');
            $dokter->save();
        } elseif ($request->input('role')=='klinik') {
            $faskes = new Hospital();
            $faskes->nama = $request->input('name');
            $faskes->save();
        }
        $password_input = $request->input('password');
        $user = new User();
         $salt = uniqid(mt_rand(), true);
            $salted_password = $password_input.$salt;
            $password_secure = hash('sha256', $salted_password);
            $hash = md5( rand(0,1000) );
        $user->salt = $salt;
        $user->hash = $hash;
        $user->password = $password_secure;
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->role = $request->input('role');
        $user->tglLahir = $request->input('tglLahir');
        $user->jenisKelamin = $request->input('jenisKelamin');
        $user->telp = $request->input('telp');
        $user->statusNikah = null;
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
        $user->nomorRM = array();
        $user->asuransi = array();
        $user->favorit = array();
        $user->verified = FALSE;
        $user->save();
        return response()->json([
            'status'  => 200,
            'data'    => $user,
            'message' => 'Registration Success']);
    }

    public function validateUser(Request $request)
    {   
        $hash = $request->input('hash');
        $exist = User::where('hash',$hash)->count();
        if($exist==1){
            $user = User::where('hash',$hash)->first();
            $user->verified = TRUE;
            $user->save();

            return response()->json([
                'status'  => 200,
                'data'    => $user,
                'message' => 'Registration Success']);
        }
        return response()->json([
            'status'  => 200,
             'data'    => $exist,
            'message' => 'Hash tidak valid']);
    }

    public function update(Request $request)
    {
        
        $data = User::find($request->input('id'));
        $data->update($request->all());
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'update berhasil']);
    }

    public function updatePassword(Request $request)
    {
        $data = User::find($request->input('id'));
        if($data){
            $old_password = $request->input('old_password');
            $old_salt = $data->salt;
            $salted_old_password = $old_password.$old_salt;
            $old_password_secure = hash('sha256',$salted_old_password);
            $password_current = $data->password;
            if($old_password_secure === $password_current)
            {
                $salt = uniqid(mt_rand(), true);
                $password_new = $request->input('new_password');
                $salted_password = $password_new.$salt;
    
                $password_secure = hash('sha256', $salted_password);
                $updated = User::where('id', $request->input('id'))->
    
                    update(['password' => $password_secure,
                            'salt' => $salt
                
                ]);

                return response()->json([
                    'status'  => 200,
                    'data'    => $data,
                    'message' => 'update password berhasil']);
            }
            
            else
            {
                return response()->json([
                    'status'  => 200,
                    'message' => 'update password gagal, password lama tidak sesuai']);
            }
            
        }
        else{
            return response()->json([
                'status'  => 200,
                'message' => 'user tidak ditemukan']);
        }
    
    }
    public function test()
    {
        $dt = Carbon::now()->toFormattedDateString();
      //mengirim Email
    $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
      $domain = "mg.medup.id";
      $name = "Dedy";
      $kode = "www.google.com";
      $view = view('register_email',['date' => $dt,
      'name' => $name,
      'kode' => $kode
      ]);
# Make the call to the client.


      try {
          $result = $mgClient->sendMessage($domain,
              array('from' => 'Mailgun Sandbox <postmaster@sandboxf54d42f860794de08933113a86348142.mailgun.org>',
                  'to' => 'Dedy Kurniawan Santoso <dedysmd@hotmail.com>',
                  'subject' => 'Register medup',
                  'html' => $view));
      } catch (MissingRequiredMIMEParameters $e) {
      }

        return response()->json([
            'status'  => 200,
            'data'    => $name,
            'message' => $result,
            'ok to' => 'ok'
        ]);
    
    }

    public function updateKesehatan(Request $request)
    {
        $data = User::find($request->id);
        $data->kesehatan = array("golDarah" => $request->input('golDarah'),
            "rhDarah" => $request->input('rhDarah'),
            "berat" => $request->input('berat'),
            "tinggi" => $request->input('tinggi'),
            "alergi" => $request->input('alergi'),
            "penyakit" => $request->input('penyakit'),
            "merokok" => $request->input('merokok')

        );
        $data->save();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Success']);
    }

    public function updateAsuransi(Request $request)
    {
        $data = User::find($request->id);
        $data->asuransi = $request->input('asuransi');
        $data->save();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Success']);
    }

    public function gantiPassword(Request $request)
    {
        $email = $request->input('email');
        $old_password = hash('sha256', $request->input('old_password'));
        $new_password = hash('sha256', $request->input('new_password'));

        $exist = User::where('email', $email)->where('password', $old_password)->count();

        if ($exist==1) {
            $this->validate($request, [
                'new_password' => 'required|min:8'
            ]);

            $updated = User::where('email', $email)->update(['password' => $new_password]);
            return response()->json([
                'status'  => 200,
                'data'    => $updated,
                'data'    => $exist,
                'message' => 'Success']);
        } else {
            return response()->json([
                'status'  => 200,
                'message' => 'Password lama salah']);
        }
    }

    public function getByEmail(Request $request)
    {
        $email = $request->input('email');
        $data = User::where('email', $email)->get();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Successfully'
          ]);
    }

    public function forgetPassword(Request $request)
    {
        $email = $request->input('email');


        $this->validate($request, [
      'email' => 'required|email'
      ]);

        $exist = User::where('email', $email)->count();
        if ($exist==1) {
            //mengirim Email

            $salt = uniqid(mt_rand(), true);
            $dt = Carbon::now();


            $salted_data = $email.$salt;

            $token = hash('sha256', $salted_data);
            $data = new ResetPassword();
            $data->email = $email;
            $data->token = $token;
            $data->valid_date = $dt->addHours(2);
            $data->save();

            $mgClient = new Mailgun('1bcbf231660ab583d16bafb5aea16247-4836d8f5-1101d2dc');
            $domain = "sandbox81d095112fb34e199a78f41dee23c4ca.mailgun.org";
            $view = view('forget_password_template')->with('token', $token);
            # Make the call to the client.

            $email_body = "";
            try {
                $result = $mgClient->sendMessage(
                  "$domain",
                  array('from' => 'Mailgun Sandbox <postmaster@sandbox81d095112fb34e199a78f41dee23c4ca.mailgun.org>',
                      'to' => 'Dedy Kurniawan Santoso <dedysmd@hotmail.com>',
                      'subject' => 'Hello Dedy Kurniawan Santoso',
                      'html' => $view)
              );
            } catch (MissingRequiredMIMEParameters $e) {
            }
            # You can see a record of this email in your logs: https://app.mailgun.com/app/logs

            # You can send up to 300 emails/day from this sandbox server.
            # Next, you should add your own domain so you can send 10,000 emails/month for free.




            return response()->json([
            'status'  => 200,
            'data'    => $email,
            'message' => $result


          ]);
        } else {
            return response()->json([
              'status'  => 200,
              'message' => 'Email tidak ada'


          ]);
        }
    }
    public function resetPassword($id, Request $request)
    {
        $email = $request->input('email');
        $password_new = $request->input('password_new');

        $exist = ResetPassword::where('token', $id)->where('email', $email)->count();

        if ($exist==1) {
            $salt = uniqid(mt_rand(), true);
            $salted_password = $password_new.$salt;

            $password_secure = hash('sha256', $salted_password);
            $updated = User::where('email', $email)->

                update(['password' => $password_secure,
                        'salt' => $salt

            ]);



            return response()->json([
                'status'  => 200,
                'message' => $updated


            ]);
        } else {
            return response()->json([
                'status'  => 200,
                'message' => 'Link Expired'


            ]);
        }
    }

    public function kebijakanPrivasi()
    {
        return view('privacy');
    }

    public function nameSuggestion(Request $request){
        $nama = $request->input('name');
        $data = User::where('name', 'like', '%'.$nama.'%')->select('name')->limit(5)->get();

        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Successfully'
          ]);
    }
}
