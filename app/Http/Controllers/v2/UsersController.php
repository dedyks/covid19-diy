<?php namespace App\Http\Controllers\v2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\User;
use App\Users;
use App\ImageModel;
use App\ResetPassword;
use App\RekamMedis;
use App\Hospital;
use App\Doctor;
use Image;
use Illuminate\Support\Facades\File;
use Mailgun\Mailgun;
use Illuminate\Support\Facades\Auth;
use Mailgun\Messages\Exceptions\MissingRequiredMIMEParameters;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    const MODEL = "App\ImageModel";
    public $path;
    public $dimensions;
    use RESTActions;

    public function __construct()
    {
        //DEFINISIKAN PATH
        $this->path = storage_path('app/public/images');
        //DEFINISIKAN DIMENSI
        $this->dimensions = ['245', '300', '500'];
    }

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
            'firstName' => 'required',
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
            $dokter->nama = $request->input('name');
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
        $user->firstName = $request->input('firstName');
        $user->lastName = $request->input('lastName');
        $user->role = $request->input('role');
        $user->tglLahir = $request->input('tglLahir');
        $user->jenisKelamin = $request->input('jenisKelamin');
        $user->telp = $request->input('telp');
        $user->statusNikah = null;
        $user->pendidikanTerakhir = null;
        $user->pekerjaan = null;
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
            'message' => 'ada']);
    }

    

    public function updateProfile(Request $request)
    {
        
        $user = User::find($request->input('id'));
        if($user){
            $user->email = $request->input('email');
            $user->firstName = $request->input('firstName');
            $user->lastName = $request->input('lastName');
            $user->noKTP = $request->input('no_KTP');
            $user->role = $request->input('role');
            $user->tempatLahir = $request->input('tempatLahir');
            $user->tglLahir = $request->input('tglLahir');
            $user->jenisKelamin = $request->input('jenisKelamin');
            $user->alamat = $request->input('alamat');
            $user->statusNikah = $request->input('statusNikah');
            $user->pendidikanTerakhir = $request->input('pendidikanTerakhir');
            $user->pekerjaan = $request->input('pekerjaan');    
            $user->telp = $request->input('telp');
            $user->save();
            return response()->json([
                'status'  => 200,
                'data'    => $user,
                'message' => 'update berhasil']);
        }
        else
        {
            return response()->json([
                'status'  => 406,
                'message' => 'ID user tidak ditemukan']);
        }
      
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
                $data->password = $password_secure;
                $data->salt = $salt;
                $data->save();

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
        if($data){
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
        else
        {
            return response()->json([
                'status'  => 406,
                'message' => 'ID user tidak ditemukan']);
        }    
    }
    

    public function updateAsuransi(Request $request)
    {
        $data = User::find($request->id);
       if($data)
       {
        $data->asuransi = $request->input('asuransi');
        $data->save();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Success']);
       }
        else
        {
            return response()->json([
                'status'  => 406,
                'message' => 'ID user tidak ditemukan']);
        }  
        
    }


  

    public function getByEmail(Request $request)
    {
        $email = $request->input('email');
        $data = User::where('email', $email)->with('image')->get();
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
        $user = User::where('email', $email);
        $exist = $user->count();
        if ($exist==1) {
            //mengirim Email
            $userData = $user->first();
            $salt = uniqid(mt_rand(), true);
            $dt = Carbon::now();


            $salted_data = $email.$salt;

            $token = hash('sha256', $salted_data);
            $data = new ResetPassword();
            $data->email = $email;
            $data->token = $token;
            $data->valid_date = $dt->addHours(2);
            $data->save();
            $userData->token = $token;
           
            $mgClient = new Mailgun('5b6da7733c3fd9c9668903c78311cd57-bd350f28-c1c71188');
            $domain = "mg.medup.id";
           
            $view = view('forget_password_template',['date' => $dt->toFormattedDateString(),
    'data' => $userData

    ]);
            # Make the call to the client.

           
            try {
                $result = $mgClient->sendMessage(
                  $domain,
                  array('from' => 'Medup <postmaster@mg.medup.id>',
                      'to' => $userData->email,
                      'subject' => 'Reset Password MedUp',
                      'html' => $view)
              );
            } catch (MissingRequiredMIMEParameters $e) {
            }

            return response()->json([
            'status'  => 200,
            'terkirim ke'    => $email,
            'message' => 'Email Terkirim',
            'mailgun log' => $result


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
        
        $password_new = $request->input('password_new');
        $dt = Carbon::now();
        $data = ResetPassword::where('token', $id);
        $exist =$data->count();

        if ($exist==1) {
            
            $valid = $data->where('valid_date','>',$dt)->count();
            $userData = ResetPassword::where('token', $id)->first();

           if($valid==1)
           {
            $salt = uniqid(mt_rand(), true);
            $salted_password = $password_new.$salt;

            $password_secure = hash('sha256', $salted_password);
            $updated = User::where('email', $userData->email)->

                update(['password' => $password_secure,
                        'salt' => $salt

            ]);
            //tidak perlu dihapu,untuk Log
            // $deleted = $data->delete();



            return response()->json([
                'status'  => 200,
                'message' => "reset password berhasil"


            ]);
           }
           else
           {
            return response()->json([
                'status'  => 200,
                'message' => 'Token Expired'


            ]);
           }
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

    public function photoProfile(Request $request){
        $m = self::MODEL;
        $data = new ImageModel;
        $data = $request->all();
        $this->validate($request, [
            'image' => 'required|image|mimes:jpg,png,jpeg'
        ]);

        //JIKA FOLDERNYA BELUM ADA
        if (!File::isDirectory($this->path)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($this->path);
        }

        //MENGAMBIL FILE IMAGE DARI FORM
        $file = $request->file('image');

        //MEMBUAT NAME FILE DARI GABUNGAN TIMESTAMP DAN UNIQID()
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //UPLOAD ORIGINAN FILE (BELUM DIUBAH DIMENSINYA)
        Image::make($file)->save($this->path . '/' . $fileName);

        //LOOPING ARRAY DIMENSI YANG DI-INGINKAN
        //YANG TELAH DIDEFINISIKAN PADA CONSTRUCTOR
        foreach ($this->dimensions as $row) {
            //MEMBUAT CANVAS IMAGE SEBESAR DIMENSI YANG ADA DI DALAM ARRAY
            $canvas = Image::canvas($row, $row);
            //RESIZE IMAGE SESUAI DIMENSI YANG ADA DIDALAM ARRAY
            //DENGAN MEMPERTAHANKAN RATIO
            $resizeImage  = Image::make($file)->resize($row, $row, function($constraint) {
                $constraint->aspectRatio();
            });

            //CEK JIKA FOLDERNYA BELUM ADA
            if (!File::isDirectory($this->path . '/' . $row)) {
                //MAKA BUAT FOLDER DENGAN NAMA DIMENSI
                File::makeDirectory($this->path . '/' . $row);
            }

            //MEMASUKAN IMAGE YANG TELAH DIRESIZE KE DALAM CANVAS
            $canvas->insert($resizeImage, 'center');
            //SIMPAN IMAGE KE DALAM MASING-MASING FOLDER (DIMENSI)
            $canvas->save($this->path . '/' . $row . '/' . $fileName);
        }


        $data['user_id'] = $request->input('user_id');
        $data['imageName'] = $fileName;
        $data['imageDimension'] = implode('|', $this->dimensions);
        $data['imagePath'] = $this->path;
        //SIMPAN DATA IMAGE YANG TELAH DI-UPLOAD
        return $this->respond(Response::HTTP_CREATED, $m::create($data));
     }

     public function version(){
        $data =  DB::collection('version')->first();
        return response()->json([
            'status'  => 200,
            'data'    => $data,
            'message' => 'Successfully'
          ]);
     }
}
