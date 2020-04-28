<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function jwt(User $user)
    {
        $payload = [
        'iss' => 'lumen-jwt', // Issuer of the token
        'sub' => $user->id, // Subject of the token
        'iat' => time(), // Time when JWT was issued.
        'exp' => time() + 157784630, // Expiration time
    ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function authenticate(User $user)
    {
        $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // Find the user by email
        $user = User::where('email', $this->request->input('email'))->first();
        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the
            // below respose for now.
            return response()->json([
                'error' => 'Email does not exist.',
            ], 400);
        }

        // Verify the password and generate the token
        $email = $this->request->input('email');
        $dataUser = User::where('email', $email)->first();
        $salt = $dataUser->salt;
        $password_input = $this->request->input('password');
        $salted_password = $password_input.$salt;
        $password_secure = hash('sha256', $salted_password);

        if ($data = User::where('email', $email)->where('password', $password_secure)->first()) {
            return response()->json([
                'token' => $this->jwt($user),
                'data' => $user,
            ], 200);
        }
        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.',
        ], 400);
    }

    public function daftar(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        $password_input = $request->input('password');
        $user = new User();
        $salt = uniqid(mt_rand(), true);
        $salted_password = $password_input.$salt;
        $password_secure = hash('sha256', $salted_password);
        $hash = md5(rand(0, 1000));
        $user->salt = $salt;
        $user->hash = $hash;
        $user->password = $password_secure;

        //Info
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->role = 'admin';
        $user->save();

        return response()->json([
            'data' => $user,
            'token' => $this->jwt($user),
        ], 200);
    }
}
