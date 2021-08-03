<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Common\UserType;
use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Models\Ads;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){

        $rules = [
            'email'     => 'required|email',
            'password'  => 'required',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'errors'    => $validate->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)
        ->where('type', UserType::USER)
        ->where('status', Status::ACTIVE)
        ->first();

        if(!$user){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid email or password',
            ], 400);
        }

        if(!Hash::check($request->password, $user->password)){
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid email or password',
            ], 400);
        }

        if(Auth::loginUsingId($user->id)){

            $token = Auth::user()->createToken('TutsForWeb')->accessToken;

            return response()->json([
                'status'    => 'success',
                'message'   => 'Welcome '. $user->name,
                'token'     => $token,
            ], 200);
        }
        else{

            return response()->json([
                'status'    => 'error',
                'message'   => 'Unauthorised',
            ], 401);
        }
    }

    public function register(Request $request){

        $rules = [
            'name'      => 'required',
            'email'     => 'required|email',
            'password'  => 'required',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'errors'    => $validate->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)
        ->first();

        if($user){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Email already taken',
            ], 400);
        }

        $user               = new User();
        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->password     = Hash::make($request->password);
        $user->type         = UserType::USER;
        $user->save();

        if(Auth::loginUsingId($user->id)){

            $token = Auth::user()->createToken('TutsForWeb')->accessToken;

            return response()->json([
                'status'    => 'success',
                'message'   => 'Registration Successful',
                'token'     => $token,
            ], 200);
        }
    }

    public function sendPasswordToMail(Request $request){

        $rules = [
            'email' => 'required|email',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid request',
                'errors'    => $validate->errors(),
            ], 400);
        }

        try{
            $user = User::where('email', $request->email)
            ->where('type', UserType::USER)
            ->first();

            if(!$user){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'No user with this email',
                ], 401);
            }

            $newPassword = uniqid();

            $details = [
                'name'      => $user->name,
                'password'  => $newPassword,
            ];

            User::where('email', $request->email)
            ->update([
                'password'  => Hash::make($newPassword),
            ]);

            Mail::to($request->email)->send(new PasswordReset($details));

            return response()->json([
                'status'    => 'success',
                'message'   => 'Password has been sended to your registered email',
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function myProfile(){
        
        try {
            $user = User::where('id', Auth::user()->id)
            ->where('type', UserType::USER)
            ->first();

            $myAds = Ads::where('customer_id', $user->id)
            ->where('status', Status::ACTIVE)
            ->where('delete_status', '!=', Status::DELETE)
            ->count();

            $myFavourite = Favorite::where('customer_id', $user->id)
            ->whereHas('Ads')
            ->count();

            return response()->json([
                'status'    => 'success',
                'message'   => 'User Profile',
                'data'      => [
                    'myads'         => $myAds,
                    'myfavourite'   => $myFavourite,
                    'user'          => $user,
                ],
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function logout(){

        try{
            if (Auth::check()) {
                Auth::user()->AauthAcessToken()->delete();
            }

            return response()->json([

                'status'    => 'success',
                'message'   => 'User Logout',

            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

}
