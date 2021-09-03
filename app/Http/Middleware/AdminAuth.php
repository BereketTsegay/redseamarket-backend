<?php

namespace App\Http\Middleware;

use App\Common\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if(Auth::user()){
            if(Auth::user()->type == UserType::ADMIN || Auth::user()->type == UserType::SUBADMIN){
                return $next($request);
            }
        }

        return redirect()->route('login.index');
    }
}
