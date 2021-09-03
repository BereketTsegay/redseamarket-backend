<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Common\UserType;
use App\Mail\PasswordReset;
use App\Models\Ads;
use App\Models\Favorite;
use App\Models\FcmClientToken;
use App\Models\User;
use Carbon\Carbon;
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

        if($user->type == UserType::ADMIN || $user->type == UserType::SUBADMIN){

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

        $inActiveAd = Ads::where('status', Status::INACTIVE)
        ->count();

        $activeAd = Ads::where('status', Status::ACTIVE)
        ->count();

        $user = User::where('type', UserType::USER)
        ->where('delete_status', '!=', Status::DELETE)
        ->count();

        $year = Carbon::now()->year;
        $nextYear = Carbon::now()->addYear()->year;
        
        // Order Chart

        $months = [];
        
        for($i = 1; $i <= Carbon::now()->month; $i++){
            if($i<10){
                $months[] = '0'.$i;
            }
            else{
                $months[] = ''.$i;
            }
            
        }

        $adCount = [];

        foreach($months as $month){

            $adCount[] = Ads::where('delete_status', '!=', Status::DELETE)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        }

        $adCount = json_encode($adCount, JSON_NUMERIC_CHECK);

        $userCount = [];

        foreach($months as $month){

            $userCount[] = User::where('delete_status', '!=', Status::DELETE)
            ->where('type', UserType::USER)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        }
        
        $userCount = json_encode($userCount, JSON_NUMERIC_CHECK);

        return view('dashboard', compact('inActiveAd', 'activeAd', 'user', 'adCount', 'userCount'));
    }

    public function userIndex(){

        $user = User::where('status', Status::ACTIVE)
        ->where('type', UserType::USER)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('user.user_list', compact('user'));
    }

    public function userView($id){

        $user = User::where('id', $id)
        ->first();

        $activeAd = Ads::where('customer_id', $id)
        ->where('status', Status::ACTIVE)
        ->where('delete_status','!=', Status::DELETE)
        ->count();

        $inactiveAd = Ads::where('customer_id', $id)
        ->where(function($q){
            $q->orwhere('status', Status::REQUEST)
            ->orwhere('status', Status::INACTIVE);
        })
        ->where('delete_status','!=', Status::DELETE)
        ->count();

        $favourite = Favorite::where('customer_id', $id)
        ->Has('Ads')
        ->count();

        return view('user.view_user', compact('user', 'activeAd', 'inactiveAd', 'favourite'));
    }

    public function userEdit($id){

        $user = User::where('id', $id)
        ->first();

        return view('user.edit_user', compact('user'));
    }

    public function userUpdate(Request $request, $id){
        
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
        ]);

        if($request->status == 'on'){
            $status = Status::ACTIVE;
        }
        else{
            $status = 0;
        }

        User::where('id', $id)
        ->update([
            'email'     => $request->email,
            'name'  => $request->name,
            'status'    => $status,
        ]);

        session()->flash('success', 'User details has been changed');
        return redirect()->route('user.index');
    }

    public function userAds($type, $id){

        if($type == 'active'){

            $ad = Ads::where('delete_status', '!=', Status::DELETE)
            ->where('customer_id', $id)
            ->where('status', Status::ACTIVE)
            ->paginate(10);
        }
        elseif($type == 'inactive'){

            $ad = Ads::where('delete_status', '!=', Status::DELETE)
            ->where('customer_id', $id)
            ->where(function($a){
                $a->orwhere('status', Status::REQUEST)
                ->orwhere('status', Status::INACTIVE);
            })
            ->paginate(10);

        }
        else{

            $ad = Ads::where('delete_status', '!=', Status::DELETE)
            ->where('status', Status::ACTIVE)
            ->whereHas('Favourite', function($a) use($id){
                $a->where('customer_id', $id);
            })
            ->paginate(10);
        }

        return view('user.ad_list', compact('ad', 'id', 'type'));
    }

    public function userChangePassword(Request $request, $id){
        
        $request->validate([
            'password'  => 'required|confirmed',
        ]);

        User::where('id', $id)
        ->update([
            'password'  => Hash::make($request->password),
        ]);

        session()->flash('success', 'Password has been changed');
        return redirect()->route('user.index');
    }

    public function tokenStore(Request $request){

        $user = User::where('email', $request->email)
        ->first();

        if($user){

            $userToken = FcmClientToken::where('user_id', $user->id)
            ->first();

            if($userToken){
                FcmClientToken::where('user_id', $user->id)
                ->update([
                    'token' => $request->token,
                ]);
            }
            else{
                $fcmToken       = new FcmClientToken();
                $fcmToken->user_id  = $user->id;
                $fcmToken->token    = $request->token;
                $fcmToken->save();
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Token stored',
            ], 200);
        }
        else{

            return response()->json([
                'status'    => 'error',
                'message'   => 'No user with this email',
            ], 400);
        }
    }

    public static function sendNotification(){
        
        $firebaseToken = FcmClientToken::whereNotNull('token')
        ->pluck('token')
        ->all();
        
        $SERVER_API_KEY = 'AAAA74ITCps:APA91bEgE_FZKMG43FU0nBdoOv_yfutK_a5DK4GLulG6Q_ZiSl7dWhkJRaQGFQnvlHVbHz8qr5KdZlzVnogvhOjz4uV3XtDwMfyAG3vefpHzYIbFvQ1Pg6SE3pbl8M_zOy7vAFZLoDGN';

        $data = [

            "registration_ids" => $firebaseToken,

            "notification" => [

                "title" => 'New product',

                "body" => 'New Product is created',  


            ]

        ];

        $dataString = json_encode($data);

    

        $headers = [

            'Authorization: key=' . $SERVER_API_KEY,

            'Content-Type: application/json',

        ];

    

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        
        curl_close($ch);
        
        return response()->json($response);
    }
}
