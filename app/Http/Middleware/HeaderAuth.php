<?php

namespace App\Http\Middleware;

use Closure;

class HeaderAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hash = md5(date('Y').env('APP_NAME'));
        if($request->header('Authorization') != $hash){
            //return response()->json(['success'=> false, 'error'=> 'Authorization failed'], 401);
        }

        return $next($request)
          ->header('Access-Control-Allow-Origin', '*')
          // ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
          // ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization')
          ;
    }
}
