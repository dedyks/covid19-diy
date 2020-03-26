<?php namespace App\Http\Controllers\v2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Book;
// use App\ResetPassword;
// use App\RekamMedis;
use App\Hospital;
use App\User;
// use Mailgun\Mailgun;
// use Mailgun\Messages\Exceptions\MissingRequiredMIMEParameters;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function Recap(Request $request){
        $kode = $request->input('faskes_id');

        $identitas = Hospital::where('_id',$kode)->select('nama','jenis')->first();
        $jumlahBook = Book::where('faskes_id',$kode)->where('book_status','BOOKED')->count();
        $jumlahConfirmed = Book::where('faskes_id',$kode)->where('book_status','CONFIRMED')->count();
        $jumlahCheckedIn = Book::where('faskes_id',$kode)->where('book_status','CHECKEDIN')->count();
        $jumlahOngoing = Book::where('faskes_id',$kode)->where('book_status','ONGOING')->count();
        $jumlahDone = Book::where('faskes_id',$kode)->where('book_status','DONE')->count();
        $jumlahAborted = Book::where('faskes_id',$kode)->where('book_status','ABORTED')->count();
      


        return response()->json([
            'status'  => 200,
            'Identitas'    => $identitas,
            'Mendaftar' => $jumlahBook,
            'Mengantri' => $jumlahCheckedIn,
            'Diperiksa' => $jumlahOngoing,
            'Selesai' => $jumlahDone,
            'Batal' => $jumlahAborted
            ]);
    }

    public function daftarOffline(Request $request){
       
        // $rekammedis = new RekamMedis();
        // $rekammedis->user_email = $request->email;
        // $rekammedis->save();

        //password
        $password_input = $request->input('password');
        $user = new User();
         $salt = uniqid(mt_rand(), true);
            $salted_password = $password_input.$salt;
            $password_secure = hash('sha256', $salted_password);
            $hash = md5( rand(0,1000) );
        $user->salt = $salt;
        $user->hash = $hash;
        $user->password = $password_secure;

        //Info
        $user->faskes_id_pendaftar = $request->input('faskes_id');
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->role = 'pasien';
        $user->tglLahir = $request->input('tglLahir');
        $user->jenisKelamin = $request->input('jenisKelamin');
        $user->telp = $request->input('telp');
        $user->statusNikah = null;
        $user->pendidikanTerakhir = $request->input('pendidikanTerakhir');
        $user->pekerjaan = $request->input('pekerjaan');
        $user->no_KTP = $request->input('no_KTP');
        $user->telp = $request->input('telp');
        $user->kesehatan = array("golDarah" => null,
            "rhDarah" => null,
            "berat" => null,
            "tinggi" => null,
            "alergi" => array(
                $request->input('alergi')
            ),
            "penyakit" => array($request->input('penyakit')),
            "merokok" => null,

        );
        $user->nomorRM = array();
        $user->asuransi = array();
        $user->favorit = array();
        $user->verified = FALSE;
        $user->save();

        $book = new Book();
        $book->faskes_id =  $request->input('faskes_id');
        $book->dokter_id = $request->input('faskes_id');
        $book->user_id = $request->input('user_id');
        $book->tgl_reservasi = $request->input('tgl_reservasi');
        $book->keterangan = $request->input('keterangan');
        $book->book_status = "BOOKED";
        $book->pembayaran = $request->input('pembayaran');
       
        $book->status_infomedis = $request->input('status_infomedis');
        $book->status_pasien = $request->input('status_pasien');
        $book->nomor_rekammedis = $request->input('nomor_rekammedis');
        $book->keluhan = $request->input('keluhan');
        $book->riwayat_alergi = $request->input('riwayat_alergi');
        $book->riwayat_penyakit = $request->input('riwayat_penyakit');
        $book->save();


        return response()->json([
            'status'  => 200,
            'user'    => $user,
            'book information' => $book,
            'message' => 'Registration Success']);
    }
}
