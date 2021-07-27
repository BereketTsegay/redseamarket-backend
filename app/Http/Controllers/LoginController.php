<?php

namespace App\Http\Controllers;

use App\Common\UserType;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function index(){

        if(Auth::user()){

            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function store(Request $request){
        
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required',
        ]);


        $user = User::where('email', $request->email)
        ->first();

        if(!$user){
            
            session()->flash('error', 'Invalid Credentials');
            return redirect()->back();
        }

        if(!Hash::check($request->password, $user->password)){
            
            session()->flash('error', 'Invalid Credentials');
            return redirect()->back();
        }

        if($user->type == UserType::ADMIN){

            $remember = $request->remember == 'checked' ? true : false;
            
            Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember);

            return redirect()->route('dashboard');
        }
        else{

            session()->flash('error', 'Unauthorized');
            return redirect()->back();
        }
        
    }

    public function forgotPasswordIndex(){

        return view('password');
    }

    public function forgotPasswordStore(Request $request){

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
        ->first();

        if(!$user){

            session()->flash('error', 'No user with "'.$request->email.'" email');
            return redirect()->back();
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

        return redirect()->route('login.index');
    }

    public function changePassword(Request $request){
        
        $request->validate([
            'current_password'  => 'required',
            'password'          => 'required|confirmed',
        ]);

        $user = User::where('id', Auth()->user()->id)
        ->first();

        if(!Hash::check($request->current_password, $user->password)){

            session()->flash('error', 'Current password is incurrect');
            return redirect()->route('dashboard');
        }

        User::where('id', Auth()->user()->id)
        ->update([
            'password'  => Hash::make($request->password),
        ]);

        Auth::logout();

        session()->flash('success', 'Password has been changed');
        return redirect()->route('login.index');
    }

    public function profile(){

        $user = User::where('id', Auth()->user()->id)
        ->first();

        return view('profile', compact('user'));
    }

    public function profileEdit($id){

        $user = User::where('id', Auth()->user()->id)
        ->first();

        return view('edit_profile', compact('user'));
    }

    public function profileUpdate(Request $request, $id){

        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email,'.$id.',id',
        ]);

        User::where('id', Auth()->user()->id)
        ->update([
            'name'      => $request->name,
            'email'     => $request->email,
        ]);

        session()->flash('success', 'Profile has been changed');
        return redirect()->route('admin.profile');
    }

    public function dashboard(){

        return view('dashboard');
    }

    public function userIndex(){

        return view('user.user_list');
    }
}
