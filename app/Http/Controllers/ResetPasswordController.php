<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\ResetPassword;
 class ResetPasswordController extends Controller
 {
 	public function requestForgotPassword(Request $request){
 		// $email = $request->email;
 		$email = 'ade';
 		$user = User::where('email',$email)->first();
 		if($user != null){
 			$reset = ResetPassword::where('email',$email)->first();
 			if($reset == null){
 				do {
 					$token = str_random(60);
 				} while (count(ResetPassword::where('token',$token)->get()) != 0);
 				$resetpassword = new ResetPassword;
 				$resetpassword->email = $email;
 				$resetpassword->token = $token;
 				$resetpassword->valid_date = date('Y-m-d h:i:s', strtotime("+1 days"));
 				$resetpassword->save();
 				return $resetpassword;
 			}
 			else{
 				return 'Please check your email again!';
 			}
 		}else{
 			return 'Email not found';
 		}
 	}

 	public function changePassword($token, Request $request){
 		$password = $request->password;
 		$getemail = ResetPassword::where('token',$token)->first();
 		if($getemail != null){
 			$user = User::where('email',$getemail->email)->first();
 			$user->password = Hash::make($password);
 			$user->save();

 			$getemail->delete();
 			return $user;
 			}
 		else{
 			return 'Token not found';
 			}
 		
 	}

 }