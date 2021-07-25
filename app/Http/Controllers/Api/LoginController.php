<?php

namespace App\Http\Controllers\Api;

use App\Common\Status;
use App\Common\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
}
