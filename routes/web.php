<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('version', 'v2\UsersController@version');
$router->post('submit', 'ScreeningController@add');

$router->post('daftar', 'AuthController@daftar');


$router->post(
    'auth/login',
    [
       'uses' => 'AuthController@authenticate',
    ]
);

$router->post('v2/test/login', 'AuthController@postLogin');
$router->group(['prefix' => 'v2'], function () use ($router) {
    $router->post(
        'auth/login',
        [
           'uses' => 'AuthController@authenticate',
        ]
    );
    $router->get('/test/sarjito', 'v2\HospitalController@sarjito');

    //Suggestion
    $router->post('suggestion/wilayah', 'v2\SearchController@suggestionWilayah');
    //search Faskes
    $router->post('/web/search_faskes', 'v2\Search\SearchHospitalController@search_faskes');
    $router->post('/web/search_faskes/nearest', 'v2\Search\SearchHospitalController@searchFaskesNearest');

    $router->post('/web/search-faskes/by-jenis/', 'v2\Search\SearchHospitalController@faskes_jenis');
    $router->post('/web/search-faskes/by-jenis/by-jenis-faskes/', 'v2\Search\SearchHospitalController@faskes_jenis_tipe');
    $router->post('/web/search/nearest', 'v2\Search\SearchHospitalController@nearest');
    $router->post('/web/search/nearest/rs', 'v2\Search\SearchHospitalController@nearestRS');

    $router->post('/web/search_doctor', 'v2\Search\SearchDoctorController@search_doctor');

    $router->post('/messenger/batchedelweisssarjito', 'v2\MedupMessengerController@batchRegister');
    $router->post('/messenger/hook', 'v2\MedupMessengerController@sendWebhook');

    /*
           * Routes for resource hubungi-kami
           */
    $router->post('kaspro/partner/subcscriber', 'v2\KasproController@register');
    $router->get('kaspro/partner/subcscriber/wallet', 'v2\KasproController@subscribersAccountInquiry');
    $router->get('kaspro/payu/session', 'v2\KasproController@login');
    $router->get('kaspro/register/otp', 'v2\KasproController@getOtp');
    $router->get('kaspro/kasproagent', 'v2\KasproController@kasproAgent');
    $router->post('kaspro/agents', 'v2\KasproController@agentRegister');
    $router->post('kaspro/premium/upgrade', 'v2\KasproController@upgrade');
    $router->post('kaspro/transfers', 'v2\KasproController@paymentP2P');
    $router->post('kaspro/wallet/deallocate', 'v2\KasproController@getFund');

    // $router->post('kaspro/transfers', 'v2\KasproController@P2PTransfer');

    $router->get('hubungi-kami', 'v2\HubungiKamisController@all');
    $router->get('hubungi-kami/{id}', 'v2\HubungiKamisController@get');
    $router->post('hubungi-kami', 'v2\HubungiKamisController@add');
    $router->put('hubungi-kami/{id}', 'v2\HubungiKamisController@put');
    $router->delete('hubungi-kami/{id}', 'v2\HubungiKamisController@remove');

    //antrian
    $router->post('antrian', 'v2\BookController@antrian');
    $router->post('antrian/add', 'v2\BookController@antrianAdd');
    $router->post('antrian/reset', 'v2\BookController@antrianReset');
    $router->post('antrian/faskes/dokter/sesi', 'v2\BookController@antrianFaskesDokterSesi');

    //request demo
    $router->post('request-demo', 'v2\RequestDemosController@add');

    $router->post('register', 'v2\UsersController@register');
    $router->post('register/validate', 'v2\UsersController@registerValidate');

    //otp
    $router->post('otp', 'v2\OtpController@createTCASTSMS');
    $router->post('otp/email', 'v2\OtpController@createEmail');
    $router->get('otp/balance', 'v2\OtpController@balance');
    $router->get('otp/status', 'v2\OtpController@status');
    $router->post('otp/check_kode', 'v2\OtpController@checkKode');

    //suggestion
    $router->get('suggestion/doctor', 'v2\Search\SearchDoctorController@suggestion');
    $router->get('suggestion/faskes', 'v2\Search\SearchHospitalController@suggestion');

    //Fasilitas FasilitasKesehatanController
    $router->get('fasilitas_kesehatan', 'v2\FasilitasKesehatanController@getAll');
    $router->post('faskes/list_doctor', 'v2\DoctorController@getDoctorByFaskes');
    $router->post('profile/update', 'v2\UsersController@update');
    $router->post('fasilitas-kesehatan/asuransi', 'v2\FasilitasKesehatanController@asuransiFaskes');

    $router->get('asuransi', 'v2\AsuransiController@getAll');
    $router->get('asuransi/{id}', 'v2\AsuransisController@get');

    $router->get('doctor/{id}', 'v2\DoctorController@getDoctorById');
    $router->get('hospital/{id}', 'v2\HospitalController@getHospitalById');
    $router->get('hospital/faskes_kode/{id}', 'v2\HospitalController@getHospitalByKode');

    $router->post('profile', 'v2\UsersController@profile');
    $router->put('profile/edit', 'v2\UsersController@profileEdit');

    $router->get('test', 'v2\BookController@asuransiFaskes');
    $router->get('spesialisasi', 'v2\SearchController@getSpesialis');

    //book

    $router->post('book/blast-email', 'v2\BookController@blastEmail');
    $router->post('book/cancel-blast-email', 'v2\BookController@cancelBlastEmail');

    //forget password
    $router->post('user/forgetpassword', 'v2\UsersController@forgetPassword');
    $router->post('user/forgetpassword/{id}', 'v2\UsersController@resetPassword');

    $router->post('sendTelegram', 'v2\BookController@sendTelegram');
    $router->post('sendWa', 'v2\BookController@sendWa');

    $router->get('delete/edeleweiss', 'v2\MedupMessengerController@deleteEdelweiss');
});

$router->group(['prefix' => 'v2',
                'middleware' => 'jwt.auth',
            ], function () use ($router) {
                //Medup Messenger resources
                $router->get('messenger/boards', 'v2\MedupMessengerController@getBoards');

                $router->post('messenger/roles', 'v2\MedupMessengerController@getRoles');

                $router->post('messenger/users', 'v2\MedupMessengerController@registerUserstoTeam');
                $router->get('messenger/users/{id}/detail', 'v2\MedupMessengerController@getDetailUser');

                $router->get('messenger/user/tenagamedis', 'v2\MedupMessengerController@getTenagaMedis');
                $router->get('messenger/user/pasien', 'v2\MedupMessengerController@getUsers');

                $router->get('messenger/user/search', 'v2\MedupMessengerController@searchUsers');

                $router->put('messenger/users/{id}', 'v2\MedupMessengerController@updateUsers');

                $router->put('messenger/users/{id}/code', 'v2\MedupMessengerController@resetUsersCode');

                $router->delete('messenger/users/{id}', 'v2\MedupMessengerController@deleteUsers');

                $router->post('messenger/teams', 'v2\MedupMessengerController@createTeams');
                $router->get('messenger/teams', 'v2\MedupMessengerController@getTeams');
                $router->put('messenger/teams/{id}', 'v2\MedupMessengerController@updateTeams');

                $router->get('messenger/dashboard/users', 'v2\MedupMessengerController@getDashboardUsers');
                $router->post('messenger/sardjito/kunjungan', 'v2\MedupMessengerController@get_sardjito_kunjungan');
                $router->post('messenger/sardjito/medis', 'v2\MedupMessengerController@get_sardjito_medis');
                //search Faskes
                $router->post('/search_faskes/', 'v2\Search\SearchHospitalController@search_faskes');
                $router->post('/search_faskes/nearest', 'v2\Search\SearchHospitalController@searchFaskesNearest');

                $router->post('/search-faskes/by-jenis/', 'v2\Search\SearchHospitalController@faskes_jenis');
                $router->post('/search-faskes/by-jenis/by-jenis-faskes/', 'v2\Search\SearchHospitalController@faskes_jenis_tipe');
                $router->post('/search/nearest', 'v2\Search\SearchHospitalController@nearest');
                $router->post('/search/nearest/rs', 'v2\Search\SearchHospitalController@nearestRS');

                // Front Page Search Doctor
                $router->get('/doctor/', 'v2\Search\SearchDoctorController@index');

                //Search Doctor
                $router->post('/search_doctor', 'v2\Search\SearchDoctorController@search_doctor');
                $router->post('/search_doctor/spesialis', 'v2\Search\SearchDoctorController@search_doctor_spesialis');

                $router->post('rekam_medis', 'v2\RekamMedisController@create');
                $router->post('rekam_medis/view', 'v2\RekamMedisController@view');

                $router->get('diagnosa', 'v2\DiagnosaController@get');

                //book
                $router->get('notif', 'v2\FirebaseNotification');
                $router->post('book', 'v2\BookController@book');
                $router->post('book-faskes', 'v2\BookController@bookFaskes');

                $router->post('book/keluarga', 'v2\BookController@bookKeluarga');

                $router->post('book/cancel', 'v2\BookController@CancelBooking');

                $router->post('book/faskes', 'v2\BookController@GetBookByFaskes');
                $router->post('suggestion/faskes', 'v2\Search\SearchHospitalController@suggestion');

                $router->post('book/get', 'v2\BookController@antrian');

                $router->put('book/{id}', 'v2\BookController@update');

                //Doctor

                $router->post('doctor/add', 'v2\DoctorController@addDoctor');

                $router->put('doctor/edit', 'v2\DoctorController@editDetail');

                $router->delete('doctor/{id}/delete', 'v2\DoctorController@delete');

                /*
                 * Routes for resource asuransi
                 */
                $router->post('asuransi/add', 'v2\AsuransisController@add');
                $router->put('asuransi/{id}', 'v2\AsuransisController@put');
                $router->delete('asuransi/{id}', 'v2\AsuransisController@remove');
                //$router->get('asuransi','AsuransiController@getAll');

                /*
                 * Routes for resource user
                 */
                $router->get('user', 'v2\UsersController@all');
                $router->get('user/{id}', 'v2\UsersController@get');
                $router->post('user', 'v2\UsersController@add');
                $router->post('user/email', 'v2\UsersController@getByEmail');

                $router->put('user/{id}', 'v2\UsersController@put');
                $router->delete('user/{id}', 'v2\UsersController@remove');

                $router->get('kebijakan-privasi', 'v2\UsersController@kebijakanPrivasi');
                $router->post('user/validate', 'v2\UsersController@validateUser');
                $router->post('user/suggestion', 'v2\UsersController@nameSuggestion');
                $router->post('user/photoprofile', 'v2\UsersController@photoProfile');

                //usercontroller
                $router->put('user/update/umum', 'v2\UsersController@updateProfile');
                $router->put('user/update/kesehatan', 'v2\UsersController@updateKesehatan');
                $router->put('user/update/asuransi', 'v2\UsersController@updateAsuransi');
                $router->put('user/update/password', 'v2\UsersController@updatePassword');
                $router->put('user/update/all', 'v2\UsersController@profileEdit');

                /*
                 * Routes for resource diagnosa
                 */
                $router->get('diagnosa', 'v2\DiagnosasController@all');
                $router->get('diagnosa/{id}', 'v2\DiagnosasController@get');
                $router->post('diagnosa', 'v2\DiagnosasController@add');
                $router->put('diagnosa/{id}', 'v2\DiagnosasController@put');
                $router->delete('diagnosa/{id}', 'v2\DiagnosasController@remove');

                /*
                 * Routes for resource dashboard
                 */
                $router->post('dashboard/recap', 'v2\DashboardController@Recap');
                $router->post('dashboard/register/offline', 'v2\DashboardController@daftarOffline');
            });

//search Faskes
$router->post('/search_faskes/', 'Search\SearchHospitalController@search_faskes');
$router->post('/search-faskes/by-jenis/', 'Search\SearchHospitalController@faskes_jenis');
$router->post('/search-faskes/by-jenis/by-jenis-faskes/', 'Search\SearchHospitalController@faskes_jenis_tipe');
$router->post('/search/nearest', 'Search\SearchHospitalController@nearest');
$router->post('/search/nearest/rs', 'Search\SearchHospitalController@nearestRS');

// Front Page Search Doctor
$router->get('/doctor/', 'Search\SearchDoctorController@index');

//Search Doctor
$router->post('/search_doctor/', 'Search\SearchDoctorController@search_doctor');
$router->post('/search_doctor/spesialis', 'Search\SearchDoctorController@search_doctor_spesialis');
$router->get('doctor/{id}', 'DoctorController@getDoctorById');
$router->post('doctor/faskes', 'v2\DoctorController@getDoctorByFaskes');

$router->post('rekam_medis', 'RekamMedisController@create');
$router->post('rekam_medis/view', 'RekamMedisController@view');
$router->post('register', 'UsersController@register');
$router->post('register/validate', 'UsersController@registerValidate');

$router->get('diagnosa', 'DiagnosaController@get');

//Fasilitas FasilitasKesehatanController
$router->get('fasilitas_kesehatan', 'FasilitasKesehatanController@getAll');
$router->post('faskes/list_doctor', 'Search\SearchHospitalController@dokter');
$router->post('profile/update', 'UsersController@update');
$router->post('fasilitas-kesehatan/asuransi', 'FasilitasKesehatanController@asuransiFaskes');

$router->get('hospital/{id}', 'HospitalController@getHospitalById');
$router->get('hospital/faskes_kode/{id}', 'HospitalController@getHospitalByKode');

$router->post('profile', 'UsersController@profile');
$router->put('profile/edit', 'UsersController@profileEdit');

$router->get('test', 'BookController@asuransiFaskes');
$router->get('spesialisasi', 'SearchController@getSpesialis');

//book
$router->get('notif', 'FirebaseNotification');
$router->post('book', 'BookController@book');
$router->post('book-faskes', 'BookController@bookFaskes');

$router->post('book/keluarga', 'BookController@bookKeluarga');

$router->post('book/cancel', 'BookController@CancelBooking');

$router->post('book/faskes', 'BookController@GetBookByFaskes');
$router->post('suggestion/faskes', 'Search\SearchHospitalController@suggestion');

$router->post('book/get', 'BookController@antrian');

$router->put('book/{id}', 'BookController@update');

//otp
$router->post('otp', 'OtpController@create');
$router->get('otp/balance', 'OtpController@balance');
$router->get('otp/status', 'OtpController@status');
$router->post('otp/check_kode', 'OtpController@checkKode');

//Doctor

        $router->post('doctor/add', 'DoctorController@addDoctor');

        $router->put('doctor/edit', 'DoctorController@editDetail');

        $router->delete('doctor/{id}/delete', 'DoctorController@delete');

/*
 * Routes for resource asuransi
 */
$router->get('asuransi', 'AsuransisController@all');
$router->get('asuransi/{id}', 'AsuransisController@get');
$router->post('asuransi/add', 'AsuransisController@add');
$router->put('asuransi/{id}', 'AsuransisController@put');
$router->delete('asuransi/{id}', 'AsudransisController@remove');
//$router->get('asuransi','AsuransiController@getAll');

/*
 * Routes for resource user
 */
$router->get('user', 'UsersController@all');
$router->get('user/{id}', 'UsersController@get');
$router->post('user', 'UsersController@add');
$router->post('user/email', 'UsersController@getByEmail');

$router->put('user/{id}', 'UsersController@put');
$router->delete('user/{id}', 'UsersController@remove');

$router->post('user/forgetpassword', 'UsersController@forgetPassword');
$router->post('user/forgetpassword/{id}', 'UsersController@resetPassword');
$router->get('kebijakan-privasi', 'UsersController@kebijakanPrivasi');
$router->post('user/validate', 'UsersController@validateUser');
$router->post('user/suggestion', 'UsersController@nameSuggestion');

//usercontroller
$router->put('user/update/kesehatan', 'UsersController@updateKesehatan');
$router->put('user/update/asuransi', 'UsersController@updateAsuransi');
$router->put('user/update/password', 'UsersController@gantiPassword');

/*
 * Routes for resource diagnosa
 */
$router->get('diagnosa', 'DiagnosasController@all');
$router->get('diagnosa/{id}', 'DiagnosasController@get');
$router->post('diagnosa', 'DiagnosasController@add');
$router->put('diagnosa/{id}', 'DiagnosasController@put');
$router->delete('diagnosa/{id}', 'DiagnosasController@remove');

/*
 * Routes for resource hubungi-kami
 */
$router->get('hubungi-kami', 'HubungiKamisController@all');
$router->get('hubungi-kami/{id}', 'HubungiKamisController@get');
$router->post('hubungi-kami', 'HubungiKamisController@add');
$router->put('hubungi-kami/{id}', 'HubungiKamisController@put');
$router->delete('hubungi-kami/{id}', 'HubungiKamisController@remove');

/*
 * Routes for resource dashboard
 */
$router->post('dashboard/recap', 'DashboardController@Recap');
$router->post('dashboard/register/offline', 'DashboardController@daftarOffline');
