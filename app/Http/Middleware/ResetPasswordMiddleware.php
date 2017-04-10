<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

use Auth;
use Validator;
use App\Common;
//use User;

//use Illuminate\Http\RedirectResponse ;

class ResetPasswordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   // public $user;

   //  public function __construct(User $user){
   //      $user = new User;
   //  }
   //  //$user = new User;

    public function handle($request, Closure $next)
    {
        $d = Auth::guard('api')->user();
        $choice = isset($d)? 'api_token' : 'email';
        //return response($choice);
        $request->request->add([
            'identity' => $choice ,
            'value' => $request->$choice,
            ]);

        return $next($request);
    }
}
