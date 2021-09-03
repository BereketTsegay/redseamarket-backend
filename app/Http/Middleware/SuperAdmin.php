<?php

namespace App\Http\Middleware;

use App\Common\Task;
use App\Common\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
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
        if(!Auth::user()->type == UserType::USER){

            if(Auth::user()->type == UserType::ADMIN){

                return $next($request);
            }
            elseif(Auth::user()->UserRole->role_id == Task::MANAGE_AUTHORITY){

                return $next($request);
            }
            else{
                return redirect()->route('login.index');
            }
           
        }

        return redirect()->route('login.index');
    }
}
