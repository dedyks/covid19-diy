<?php

namespace App\Http\Controllers\v2;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use App\User;
use App\Counter;
use App\UsersMessenger;
use Log;
use Validator;

class MedupMessengerController extends Controller
{
    public function __construct()
    {
    }

    public function getDashboardUsers(Request $request)
    {
        //opsional

        $limit = $request->input('limit');
        $jwt_key = env('JWT_SECRET');

        $page = $request->input('page');
        $before = $request->input('before');
        $search = $request->input('search');
        $team_id = $request->input('team_id');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            try {
                if ($request->input('paginate') == true) {
                    $response = UsersMessenger::paginate((int) $request->input('per_page'));
                } else {
                    $response = UsersMessenger::get();
                }

                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function getBoards(Request $request)
    {
        //opsional

        $limit = $request->input('limit');
        $jwt_key = env('JWT_SECRET');

        $page = $request->input('page');
        $before = $request->input('before');
        $search = $request->input('search');
        $team_id = $request->input('team_id');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/boards';
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('GET', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],
                    'query' => [
                        'limit' => $limit,
                        'page' => $page,
                        'before' => $before,
                        'search' => $search,
                        'team_id' => $team_id,
                    ],
                    // 'json' => $json,
                    'timeout' => 30,
                ]);
                // $response = $request->send();
                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function getRoles(Request $request)
    {
        $team_id = $request->input('team_id');
        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);
        if (in_array($team_id, $user->team_id)) {
            if ($user->role === 'admin') {
                $data = UsersMessenger::where('team_id', $team_id)->where('is_verified', true)->distinct('name')->select('name')->get();

                return response()->json([
                'data' => $data,
            ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized, not admin of team_id',
            ], 400);
        }
    }

    public function getUsers(Request $request)
    {
        //opsional

        $limit = $request->input('limit');

        $page = $request->input('page');
        $before = $request->input('before');
        $search = $request->input('search');
        $is_verified = $request->input('is_verified');
        $is_activated = $request->input('is_activated');
        $team_id = $request->input('team_id');

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));
        $user = User::find($decoded->sub);
        if (in_array($team_id, $user->team_id)) {
            if ($user->role === 'admin') {
                if ($request->input('paginate') == true) {
                    $data = UsersMessenger::where('team_id', $team_id)->where('role', 'member')->where('is_verified', false)
                        ->when($search, function ($query, $search) {
                            return $query->where('title', 'like', '%'.$search.'%');
                        })
                        ->paginate((int) $request->input('per_page'));
                } else {
                    $data = UsersMessenger::where('team_id', $team_id)->where('role', 'member')->where('is_verified', false)
                        ->when($search, function ($query, $search) {
                            return $query->where('title', 'like', '%'.$search.'%');
                        })
                        ->get();
                }

                return response()->json([
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized, not admin of team_id',
            ], 400);
        }
    }

    public function getTenagaMedis(Request $request)
    {
        //opsional

        $limit = $request->input('limit');

        $page = $request->input('page');
        $before = $request->input('before');
        $search = $request->input('search');
        $is_verified = $request->input('is_verified');
        $is_activated = $request->input('is_activated');
        $team_id = $request->input('team_id');

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));
        $user = User::find($decoded->sub);
        if (in_array($team_id, $user->team_id)) {
            if ($user->role === 'admin') {
                if ($request->input('paginate') == true) {
                    $matchThese = [
                        'role' => 'admin',
                        'team_id' => $team_id,
                    ];

                    $dokter = UsersMessenger::where('team_id', $team_id)
                        ->where('role', 'member')->where('is_verified', true)
                        ->when($search, function ($query, $search) {
                            return $query->where('name', 'like', '%'.$search.'%');
                        })
                        ->get();

                    $majorData = UsersMessenger::where($matchThese)
                        ->when($search, function ($query, $search) {
                            return $query->where('name', 'like', '%'.$search.'%');
                        })

                        ->get();
                    $data = $majorData->merge($dokter);
                } else {
                    $matchThese = [
                        'role' => 'admin',
                        'team_id' => $team_id,
                    ];

                    $dokter = UsersMessenger::where('team_id', $team_id)
                        ->where('role', 'member')->where('is_verified', true)
                        ->when($search, function ($query, $search) {
                            return $query->where('name', 'like', '%'.$search.'%');
                        })
                        ->get();

                    $majorData = UsersMessenger::where($matchThese)
                        ->when($search, function ($query, $search) {
                            return $query->where('name', 'like', '%'.$search.'%');
                        })

                        ->get();
                    $data = $majorData->merge($dokter);

                    // ->orWhere(function ($query) {
                    //     $query->where('role', '<>','admin')
                    //     ->where('is_verified', true);
                    // })
                }

                return response()->json([
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized, not admin of team_id',
            ], 400);
        }
    }

    public function searchUsers(Request $request)
    {
        //opsional

        $limit = $request->input('limit');

        $page = $request->input('page');
        $before = $request->input('before');
        $search = $request->input('search');
        $is_verified = $request->input('is_verified');
        $is_activated = $request->input('is_activated');
        $team_id = $request->input('team_id');
        $role = $request->input('role');
        $is_verified = (bool) $request->input('is_verified');

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));
        $user = User::find($decoded->sub);
        if (in_array($team_id, $user->team_id)) {
            if ($user->role === 'admin') {
                if ($request->input('paginate') == true) {
                    $data = UsersMessenger::where('team_id', $team_id)->where('name', 'like', '%'.$search.'%')->orWhere('title', 'like', '%'.$search.'%')->where('role', $role)->where('is_verified', $is_verified)->paginate((int) $request->input('per_page'));
                } else {
                    $data = UsersMessenger::where('team_id', $team_id)->where('name', 'like', '%'.$search.'%')->orwhere('title', 'like', '%'.$search.'%')->where('role', $role)->where('is_verified', $is_verified)->get();
                }

                return response()->json([
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized, not admin of team_id',
            ], 400);
        }
    }

    public function getDetailUser(Request $request, $id)
    {
        //opsional

        $team_id = $request->input('team_id');
        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));
        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            $data = UsersMessenger::where('user_id', $id)->first();
            if (in_array($data->team_id, $user->team_id)) {
                return response()->json([
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized, not admin of team_id',
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function updateUsers(Request $request, $id)
    {
        //opsional
        $json = $request->json()->all();

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/users/'.$id;
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('PUT', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],

                    'json' => $json,
                    'timeout' => 30,
                ]);
                $statusCode = $response->getStatusCode();
                $obj = json_decode($response->getBody());
                if ($statusCode == 200) {
                    $userData = UsersMessenger::where('user_id', $id)->first();
                    $userData->is_activated = $obj->user->is_activated;
                    $userData->name = $obj->user->name;
                    $userData->email = $obj->user->email;
                    $userData->is_verified = $obj->user->is_verified;
                    $userData->role = $obj->user->role;
                    $userData->title = $obj->user->title;
                    $userData->summary = $obj->user->summary;
                    $userData->only_know_user_ids = $obj->user->only_know_user_ids;
                    $userData->save();

                    return response()->json([
                        'message' => 'User deleted',
                        'response from Wideboard' => $obj,
                        'response from Medup' => $userData,
                    ], 200);
                } else {
                    return response()->json([
                        'error' => $obj,
                    ], 400);
                }
                // $response = $request->send();
                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function deleteUsers(Request $request, $id)
    {
        //opsional
        $json = $request->json()->all();

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/users/'.$id;
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('DELETE', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],

                    'query' => [
                        'new_admin_id' => $request->input('new_admin_id'),
                    ],
                    'timeout' => 30,
                    'http_errors' => false,
                ]);
                $statusCode = $response->getStatusCode();
                $obj = json_decode($response->getBody());
                if ($statusCode == 200) {
                    $userData = UsersMessenger::where('user_id', $id)->delete();

                    return response()->json([
                        'message' => 'User deleted',
                        'response from Wideboard' => $obj,
                        'response from Medup' => $userData,
                    ], 200);
                } elseif ($statusCode == 404) {
                    $userData = UsersMessenger::where('user_id', $id)->delete();

                    return response()->json([
                        'message' => 'User deleted from DB Medup',
                        'response from Wideboard' => $obj,
                        'response from Medup' => $userData,
                    ], 200);
                } else {
                    return response()->json([
                        'error' => $obj,
                    ], 400);
                }
                // $response = $request->send();
                return  $response;
            } catch (GuzzleHttp\Exception\ClientException $e) {
                //             echo Psr7\str($e->getRequest());
                //      if ($e->hasResponse()) {
                //     echo Psr7\str($e->getResponse());
                // }
                //             // For handling exception.
                //             // echo Psr7\str($e->getRequest());
                //             // echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function updateTeams(Request $request, $id)
    {
        //opsional
        $json = $request->json()->all();

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/teams/'.$id;
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('PUT', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],

                    'json' => $json,
                    'timeout' => 30,
                ]);
                $statusCode = $response->getStatusCode();
                $obj = json_decode($response->getBody());

                // $response = $request->send();
                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function resetUsersCode(Request $request, $id)
    {
        //opsional
        $json = $request->json()->all();

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/users/'.$id.'/code';
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('PUT', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],

                    'query' => [
                        'new_admin_id' => $request->input('new_admin_id'),
                    ],
                    'timeout' => 30,
                ]);
                $statusCode = $response->getStatusCode();
                $obj = json_decode($response->getBody());
                if ($statusCode == 200) {
                    $userData = UsersMessenger::where('user_id', $id)->first();
                    $userData->code = $obj->user->code;
                    $userData->save();

                    return response()->json([
                        'message' => 'User code reset success',
                        'response from Wideboard' => $obj,
                        'response from Medup' => $userData,
                    ], 200);
                } else {
                    return response()->json([
                        'error' => $obj,
                    ], 400);
                }
                // $response = $request->send();
                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function createTeams(Request $request)
    {
        //opsional
        $json = $request->json()->all();

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);

        // return $json['team_id'];
        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/teams';
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('POST', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],

                    'json' => $json,
                    'timeout' => 30,
                ]);
                // $response = $request->send();
                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function getTeams(Request $request)
    {
        //opsional

        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);
        $limit = $request->input('limit');
        $page = $request->input('page');
        $before = $request->input('before');
        $search = $request->input('search');
        // return $json['team_id'];
        if ($user->role === 'admin') {
            try {
                $endpoint = env('MEDUPMESSENGER_URL').'/teams';
                // return $endpoint;

                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('GET', $endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],

                    'query' => [
                        'limit' => $limit,
                        'page' => $page,
                        'before' => $before,
                        'search' => $search,
                    ],
                    'timeout' => 30,
                ]);
                // $response = $request->send();
                return  $response;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 400);
        }
    }

    public function registerUserstoTeam(Request $request)
    {
        //opsional
        Log::debug('[MedupMessengerController][registerUserstoTeam]');
        $json = $request->json()->all();
        $jwt_key = env('JWT_SECRET');
        $decoded = JWT::decode($request->input('token'), $jwt_key, array('HS256'));

        $user = User::find($decoded->sub);
        $client = new \GuzzleHttp\Client();
        try {
            // $response = $request->send();

            if ($user->role === 'admin') {
                Log::debug('[MedupMessengerController][registerUserstoTeam] user role is admin');
                $endpoint = env('MEDUPMESSENGER_URL').'/users';
                // return $endpoint;

                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $counter = Counter::where('field', 'pasien')->first();
                $name = $request->input('name');
                $is_verified = $request->input('is_verified');

                $role = $request->input('role');
                // return $name;
                if ($is_verified == 'true') {
                    $name = $request->input('name');
                } elseif ($is_verified == 'false' && $role == 'member') {
                    $name = 'Pasien '.$counter->value.'';
                }
                // return $name;
                $reqBody = null;
                if ($request->input('only_know_user_ids') == null || $request->input('only_know_user_ids') == '') {
                    $reqBody = [
                        'name' => $name,
                        'email' => $request->input('email'),
                        'team_id' => $request->input('team_id'),
                        'title' => $request->input('title'),
                        'summary' => $request->input('summary'),
                        'is_verified' => $request->input('is_verified'),
                        'is_activated' => $request->input('is_activated'),
                        'role' => $request->input('role'),
                    ];
                } else {
                    $reqBody = [
                        'name' => $name,
                        'email' => $request->input('email'),
                        'team_id' => $request->input('team_id'),
                        'title' => $request->input('title'),
                        'summary' => $request->input('summary'),
                        'is_verified' => $request->input('is_verified'),
                        'is_activated' => $request->input('is_activated'),
                        'role' => $request->input('role'),
                        'only_know_user_ids' => $request->input('only_know_user_ids'),
                    ];
                }
                $params = [
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    ],
                    'json' => $reqBody,
                    'http_error' => false,
                    'timeout' => 60,
                ];
                Log::debug('[MedupMessengerController][registerUserstoTeam] request http endpoint: '.$endpoint.' with params: '.json_encode($params));
                $response = $client->request('POST', $endpoint, $params);

                $statusCode = $response->getStatusCode();
                $obj = json_decode($response->getBody());
                Log::debug('[MedupMessengerController][registerUserstoTeam] http result '.json_encode($response));
                if ($statusCode == 201) {
                    Log::debug('[MedupMessengerController][registerUserstoTeam] status code is 201');
                    if ($request->input('is_verified') == 'false') {
                        Log::debug('[MedupMessengerController][registerUserstoTeam] is_verified is false');
                        $counter->increment('value');
                    }
                    Log::debug('[MedupMessengerController][registerUserstoTeam] create new user messenger');
                    $newUsers = new UsersMessenger();
                    $newUsers->user_id = $obj->user->user_id;
                    $newUsers->is_activated = $obj->user->is_activated;

                    $newUsers->team_id = $obj->team->team_id;
                    $newUsers->name = $obj->user->name;
                    $newUsers->email = $obj->user->email;
                    $newUsers->is_verified = $obj->user->is_verified;
                    $newUsers->role = $obj->user->role;
                    $newUsers->code = $obj->user->code;
                    $newUsers->title = $obj->user->title;
                    $newUsers->summary = $obj->user->summary;
                    $newUsers->only_know_user_ids = $obj->user->only_know_user_ids;

                    $newUsers->save();
                    Log::debug('[MedupMessengerController][registerUserstoTeam] register as medup user');
                    $this->registerAsUserMedup($newUsers);
                    Log::debug('[MedupMessengerController][registerUserstoTeam] user is created');

                    return response()->json([
                        'message' => 'User created',
                        'data saved' => $newUsers,
                        'data Messenger' => $obj,
                    ], 201);
                } else {
                    Log::debug('[MedupMessengerController][registerUserstoTeam] status code is not 201');

                    return response()->json([
                        'error' => $obj,
                    ], 400);
                }
            } else {
                Log::debug('[MedupMessengerController][registerUserstoTeam] user role is not admin');

                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } catch (GuzzleHttp\Exception\ClientException $e) {
            Log::debug('[MedupMessengerController][registerUserstoTeam] guzzle exception : '.json_encode($e));
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            return $responseBodyAsString;
        }
    }

    public function batchRegister(Request $request)
    {
        //opsional

        $json = $request->json()->all();
        $jwt_key = env('JWT_SECRET');
        $client = new \GuzzleHttp\Client();
        try {
            // $response = $request->send();

            $endpoint = env('MEDUPMESSENGER_URL').'/users';
            // return $endpoint;

            // $request = $client->post($endpoint);
            // $request->setBody(json_decode($json));
            $counter = Counter::where('field', 'pasien')->first();
            $name = $request->input('name');
            $is_verified = $request->input('is_verified');

            $role = $request->input('role');
            // return $name;
            if ($is_verified == 'true') {
                $name = $request->input('name');
            } elseif ($is_verified == 'false' && $role == 'member') {
                $name = 'Pasien '.$counter->value.'';
            }
            // return $name;

            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                ],
                'json' => [
                    'name' => $name,
                    'email' => $request->input('email'),
                    'team_id' => $request->input('team_id'),
                    'title' => $request->input('title'),
                    'summary' => $request->input('summary'),
                    'is_verified' => $request->input('is_verified'),
                    'is_activated' => $request->input('is_activated'),
                    'role' => $request->input('role'),
                ],
                //   'http_error' => false,
                'timeout' => 30,
            ]);

            $statusCode = $response->getStatusCode();
            $obj = json_decode($response->getBody());
            if ($statusCode == 201) {
                if ($request->input('is_verified') == 'false') {
                    $counter->increment('value');
                }
                $newUsers = new UsersMessenger();
                $newUsers->user_id = $obj->user->user_id;
                $newUsers->is_activated = $obj->user->is_activated;

                $newUsers->team_id = $obj->team->team_id;
                $newUsers->name = $obj->user->name;
                $newUsers->email = $obj->user->email;
                $newUsers->is_verified = $obj->user->is_verified;
                $newUsers->role = $obj->user->role;
                $newUsers->code = $obj->user->code;
                $newUsers->title = $obj->user->title;
                $newUsers->summary = $obj->user->summary;

                $newUsers->save();

                $this->registerAsUserMedup($newUsers);

                return response()->json([
                    'message' => 'User created',
                    'data saved' => $newUsers,
                    'data Messenger' => $obj,
                ], 201);
            } else {
                return response()->json([
                    'error' => $obj,
                ], 400);
            }
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            return $responseBodyAsString;
        }
    }

    private function registerAsUserMedup($data)
    {
        $password_input = $data->code;
        $user = new User();
        $salt = uniqid(mt_rand(), true);
        $salted_password = $password_input.$salt;
        $password_secure = hash('sha256', $salted_password);
        $hash = md5(rand(0, 1000));
        $user->salt = $salt;
        $user->hash = $hash;
        $user->password = $password_secure;
        $user->email = $data->email;
        $firstname = strtok($data->title, ' ');
        $lastname = substr(strstr($data->title, ' '), 1);

        $user->firstName = $firstname;
        $user->lastName = $lastname;
        $user->role = 'pasien';
        $user->tglLahir = null;
        $user->jenisKelamin = null;
        $user->telp = null;
        $user->statusNikah = null;
        $user->keterangan = 'MedupMessenger';
        $user->pendidikanTerakhir = null;
        $user->pekerjaan = null;
        $user->kesehatan = array(
            'golDarah' => null,
            'rhDarah' => null,
            'berat' => null,
            'tinggi' => null,
            'alergi' => array(),
            'penyakit' => array(),
            'merokok' => null,
        );
        $user->nomorRM = array();
        $user->asuransi = array();
        $user->favorit = array();
        $user->verified = false;
        $user->save();
    }

    public function get_sardjito_kunjungan(Request $request)
    {
        $norm = $request->input('norm');

        $time = time();
        $user = 'RSMMEDUP';
        $pass = 'EskU[d6W5wu&X=SQ';

        $data = $user.'&'.$time;
        $signature = hash_hmac('sha256', $data, md5($pass), true);
        $encoded_signature = base64_encode($signature);
        $url = 'http://resume.simrsdemo.tk/services/resume/get_kunjungan';
        //$url = 'http://resume.simetrisnet/services/resume/get_kunjungan';

        $client = new \GuzzleHttp\Client();
        try {
            $responseAPI = $client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-cons-id' => $user,
                    'X-Timestamp' => $time,
                    'X-Signature' => $encoded_signature,
                ],
                'json' => [
                    'norm' => $norm,
                ],
            ]);

            return $responseAPI;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function get_sardjito_medis(Request $request)
    {
        $noreg = $request->input('noreg');

        $time = time();
        $user = 'RSMMEDUP';
        $pass = 'EskU[d6W5wu&X=SQ';

        $data = $user.'&'.$time;
        $signature = hash_hmac('sha256', $data, md5($pass), true);
        $encoded_signature = base64_encode($signature);
        $url = 'http://resume.simrsdemo.tk/services/resume/get_resume';

        $client = new \GuzzleHttp\Client();
        try {
            $responseAPI = $client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-cons-id' => 'RSMMEDUP',
                    'X-Timestamp' => $time,
                    'X-Signature' => $encoded_signature,
                ],
                'json' => [
                    'noreg' => $noreg,
                    ],
            ]);

            return $responseAPI;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function sendWebhook(Request $request)
    {
        $input = $request->all();
        $type = $request->input('type');
        if ($type == '1') {
            // code...
            $validator = Validator::make($input, [
                'text' => 'required',
              ]);
            if ($validator->fails()) {
                return response()->json([
                $validator->errors(),
              ], 417);
            }

            $endpoint = env('MEDUPMESSENGER_HOOK_URL');

            $client = new \GuzzleHttp\Client();

            try {
                $json = $request->json()->all();
                $response = $client->request('POST', $endpoint, [
                    //'query' => ['t' => env('MEDUPMESSENGER_KEY')],
                    'headers' => [
                        'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                        'Content-type' => 'application/json',
                    ],
                    'json' => $json,
                    'timeout' => 30,
                ]);

                return $responseAPI;
            } catch (ClientException $e) {
                // For handling exception.
                echo Psr7\str($e->getRequest());
                echo Psr7\str($e->getResponse());
            }
        } elseif ($type == '8') {
            $validator = Validator::make($input, [
                'file' => 'required',
              ]);
            if ($validator->fails()) {
                return response()->json([
                $validator->errors(),
              ], 417);
            }
        } else {
            // code...
        }

        if ($validator->fails()) {
            return response()->json([
              $validator->errors(),
            ], 417);
        }
        $noreg = $request->input('noreg');
        $endpoint = env('MEDUPMESSENGER_URL').'/users';

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer '.env('MEDUPMESSENGER_KEY'),
                    'Content-type' => 'application/json',
                ],

                'json' => $json,
                'timeout' => 30,
            ]);

            return $responseAPI;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }
}
