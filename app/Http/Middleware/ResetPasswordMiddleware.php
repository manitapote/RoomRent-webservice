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
        // $user = new User();
        $d = Auth::guard('api')->user();
        $choice = isset($d)? 'api_token' : 'email';
        
        // $rule  = isset($d)? $user->getValidationRules(2) : $user->getValidationRules(1);
        // $validator = Validator::make($request->all(),$rule);

        // if($validator->fails())
        //     return response()->json(['status' => '0014','message' => 'Validation Error','errors' => $validator->errors()]);
        // $identity = isset($d)? 'api_token' : 'email';
        $request->request->add(['identity' => $request->$choice,]);

        return $next($request);
    }
}
