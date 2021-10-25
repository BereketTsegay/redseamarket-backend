<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Common\UserType;
use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Mail\VerifyEmail;
use App\Models\Ads;
use App\Models\Favorite;
use App\Models\Otp;
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
                'code'      => 200,
                'errors'    => $validate->errors(),
            ], 200);
        }

        $user = User::where('email', $request->email)
        ->where('type', UserType::USER)
        ->where('status', Status::ACTIVE)
        ->first();

        if(!$user){

            return response()->json([
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Invalid email or password',
            ], 200);
        }

        if(!Hash::check($request->password, $user->password)){
            
            return response()->json([
                'status'    => 'error',
                'message'   => 'Invalid email or password',
                'code'      => 400,
            ], 200);
        }

        if(Auth::loginUsingId($user->id)){

            $token = Auth::user()->createToken('TutsForWeb')->accessToken;

            return response()->json([
                'status'    => 'success',
                'code'      => 200,
                'message'   => 'Welcome '. $user->name,
                'user'      => $user->name,
                'token'     => $token,
            ], 200);
        }
        else{

            return response()->json([
                'status'    => 'error',
                'code'      => 401,
                'message'   => 'Unauthorised',
            ], 200);
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
                'code'      => '400',
                'errors'    => $validate->errors(),
            ], 200);
        }

        $user = User::where('email', $request->email)
        ->where('email_verified_flag', '!=', Status::REQUEST)
        ->first();

        if($user){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Email already taken',
                'code'      => '400',
            ], 200);
        }

        $uid = rand(000000, 999999);

        $existingUser = User::where('email', $request->email)
        ->where('email_verified_flag', Status::REQUEST)
        ->first();

        if(!$existingUser){

            $user                       = new User();
            $user->name                 = $request->name;
            $user->email                = $request->email;
            $user->password             = Hash::make($request->password);
            $user->type                 = UserType::USER;
            $user->email_verified_flag  = Status::REQUEST;
            $user->save();
        }
        else{

            User::where('email', $request->email)
            ->update([
                'name'      => $request->name,
                'password'  => Hash::make($request->password),
            ]);
        }

        Otp::where('email', $request->email)
        ->update([
            'expiry_status' => true,
        ]);

        $otp                = new Otp();
        $otp->email         = $request->email;
        $otp->otp           = $uid;
        $otp->expiry_status = false;
        $otp->attempt       = 0;
        $otp->save();

        $code = [
            'name'  => $request->name,
            'otp'   => $uid,
        ];

        Mail::to($request->email)->send(new VerifyEmail($code));

        // if(Auth::loginUsingId($user->id)){

        //     $token = Auth::user()->createToken('TutsForWeb')->accessToken;

            return response()->json([
                'status'    => 'success',
                'message'   => 'Registration Successful',
                'code'      => '200',
                // 'token'     => $token,
            ], 200);
        // }
    }

    public function vefifyEmail(Request $request){

        try{

            $rules = [
                'email' => 'required|email',
                'otp'   => 'required',
            ];

            $validate = Validator::make($request->all(), $rules);

            if($validate->fails()){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'errors'    => $validate->errors(),
                ], 400);
            }

            $otp = Otp::where('email', $request->email)
            ->where('expiry_status', false)
            ->first();

            if($otp){

                Otp::where('email', $request->email)
                ->where('expiry_status', false)
                ->update([
                    'attempt'   => $otp->attempt + 1,
                ]);
            

                if($otp->attempt > 5){

                    Otp::where('email', $request->email)
                    ->where('expiry_status', false)
                    ->update([
                        'expiry_status'   => true,
                    ]);
                    
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'You exceeded your maximum attempt',
                    ], 200);

                }

                if($otp->otp == $request->otp){

                    Otp::where('email', $request->email)
                    ->where('expiry_status', false)
                    ->update([
                        'expiry_status'   => true,
                    ]);

                    User::where('email', $request->email)
                    ->update([
                        'email_verified_flag'   => Status::ACTIVE,
                    ]);

                    $user = User::where('email', $request->email)
                    ->where('email_verified_flag', Status::ACTIVE)
                    ->first();

                    if(Auth::loginUsingId($user->id)){

                        $token = Auth::user()->createToken('TutsForWeb')->accessToken;

                        return response()->json([
                            'status'    => 'success',
                            'message'   => 'Registration Successful',
                            'code'      => '200',
                            'user'      => $user->name,
                            'token'     => $token,
                        ], 200);
                    }
                }
                else{

                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Invalid Otp',
                    ], 200);
                }
            }
            else{

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Otp expired',
                ], 200);

            }

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
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
                'code'      => '400',
                'errors'    => $validate->errors(),
            ], 200);
        }

        try{
            $user = User::where('email', $request->email)
            ->where('type', UserType::USER)
            ->first();

            if(!$user){

                return response()->json([
                    'status'    => 'error',
                    'code'      => 401,
                    'message'   => 'No user with this email',
                ], 200);
            }

            Otp::where('email', $request->email)
            ->update([
                'expiry_status' => true,
            ]);

            $uid = rand(000000, 999999);

            $otp                = new Otp();
            $otp->email         = $request->email;
            $otp->otp           = $uid;
            $otp->expiry_status = false;
            $otp->attempt       = 0;
            $otp->save();

            // $newPassword = uniqid();

            $details = [
                'name'  => $user->name,
                'otp'   => $uid,
            ];

            // User::where('email', $request->email)
            // ->update([
            //     'password'  => Hash::make($newPassword),
            // ]);

            Mail::to($request->email)->send(new PasswordReset($details));

            return response()->json([
                'status'    => 'success',
                'message'   => 'Otp has been sended to your registered email',
                'code'      => 200,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function verifyOtp(Request $request){

            $rules = [
                'email' => 'required|email',
                'otp'   => 'required|numeric',
            ];

            $validate = Validator::make($request->all(), $rules);

            if($validate->fails()){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'errors'    => $validate->errors(),
                    'code'      => '200',
                ], 200);
            }

            $otp = Otp::where('email', $request->email)
            ->where('expiry_status', false)
            ->first();

            if($otp){

                Otp::where('email', $request->email)
                ->where('expiry_status', false)
                ->update([
                    'attempt'   => $otp->attempt + 1,
                ]);
            

                if($otp->attempt > 5){

                    Otp::where('email', $request->email)
                    ->where('expiry_status', false)
                    ->update([
                        'expiry_status'   => true,
                    ]);
                    
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'You exceeded your maximum attempt',
                    ], 200);

                }

                if($otp->otp == $request->otp){

                    Otp::where('email', $request->email)
                    ->where('expiry_status', false)
                    ->update([
                        'expiry_status'   => true,
                    ]);

                    return response()->json([
                        'status'    => 'success',
                        'message'   => 'Otp has been verified',
                        'code'      => '200',
                        'token'     => '#4t5o9ke0n6_#'
                    ], 200);
                }
                else{

                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Invalid Otp',
                    ], 200);
                }
            }
            else{

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Otp expired',
                ], 200);

            }
    }

    public function passwordReset(Request $request){

        try{

            $rules = [
                'password'  => 'required|confirmed',
                'email'     => 'required|email',
            ];

            $validate = Validator::make($request->all(), $rules);

            if($validate->fails()){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'errors'    => $validate->errors(),
                    'code'      => '200',
                ], 200);
            }

            User::where('email', $request->email)
            ->update([
                'password'  => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)
            ->first();

            if(Auth::loginUsingId($user->id)){
                
                $token = Auth::user()->createToken('TutsForWeb')->accessToken;
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Password has been updated',
                'token'     => $token,
                'user'      => Auth::user()->name,
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
            ->whereIn('status', [Status::ACTIVE, Status::REQUEST, Status::INACTIVE])
            ->where('delete_status', '!=', Status::DELETE)
            ->count();

            $myFavourite = Favorite::where('customer_id', $user->id)
            ->whereHas('Ads')
            ->count();

            $viewCount = Ads::where('customer_id', Auth::user()->id)
            ->sum('view_count');

            return response()->json([
                'status'    => 'success',
                'message'   => 'User Profile',
                'code'      => 200,
                'data'      => [
                    'myads'         => $myAds,
                    'myfavourite'   => $myFavourite,
                    'user'          => $user,
                    'adsView'       => $viewCount,
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
                'code'      => 200,

            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function updateProfile(Request $request){
        
        try{
            $id = Auth::user()->id;

            $rules = [
                'name'          => 'required',
                'email'         => 'required|email|unique:users,email,'.$id.',id',
                'phone'         => 'required|numeric',
                'nationality'   => 'required',
            ];

            $validate = Validator::make($request->all(), $rules);

            if($validate->fails()){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'code'      => 400,
                    'errors'    => $validate->errors(),
                ], 200);
            }
            
            User::where('id', Auth::user()->id)
            ->update([
                'name'              => $request->name,
                'email'             => $request->email,
                'phone'             => $request->phone,
                'nationality_id'    => $request->nationality,
            ]);

            return response()->json([

                'status'    => 'success',
                'message'   => 'User profile has been updated',
                'code'      => 200,

            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong',
            ], 301);
        }
    }

    public function changePassword(Request $request){

        try{
            $rules = [
                'old_password'  => 'required',
                'password'      => 'required|confirmed',
            ];

            $validate = Validator::make($request->all(), $rules);

            if($validate->fails()){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Invalid request',
                    'code'      => 400,
                    'errors'    => $validate->errors(),
                ], 200);
            }

            $user = User::where('id', Auth::user()->id)
            ->first();

            if(!Hash::check($request->old_password, $user->password)){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Incorrect password',
                    'code'      => 400,
                ], 200);
            }

            if($request->old_password == $request->password){

                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Old and new passwords are same',
                    'code'      => 400,
                ], 200);
            }

            User::where('id', Auth::user()->id)
            ->update([
                'password'  => Hash::make($request->password),
            ]);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Password has been changed',
                'code'      => 200,
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
