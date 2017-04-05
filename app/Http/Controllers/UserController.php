<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use App\Common;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\ActivationEmail;

class UserController extends Controller 
{
	public $code;
	public $message;

	public function store(Request $request)
	{
		$path ='';
		$user = new User;

		$validator = Validator::make($request->all(), [
			'name' => 'required|min:5|max:35',
			'password' => 'required',
			'phone' => 'required',
			'email' => 'required|email|unique:users,email'
			]);

		if (!$validator->fails())
		{

			if($request->file)
			{
				$tt = $request->file;
				$path = base_path().'/resources/profileImages';

				$binary = base64_decode($tt);
				$time = $path.'/'. time().'.jpg';
				$file = fopen($time, 'wb');
				fwrite($file, $binary);
				fclose($file);
				$user->profileImageURL = $time;
			}
			$user->name = $request->name;
			$user->password = Hash::make($request->password);
			$user->email = $request->email;
			$user->phone = $request->phone;
			$user->activationToken = str_random(60);
			$user->save();

			Mail::to($user)->send(new activationEmail($user));
			$this->code = '0013';
			$this->message = Common::code($this->code);
		}
		else
		{
			$this->code = '0016';
			$this->message = $validator->errors();
		}
		return response()->json(['status' => $this->code,
				'message' =>$this->message]);
	}


	public function login( Request $request)
	{	
		$user = '';
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required'
			]);

		if(!$validator->fails())
		{
			//DB::connection()->enableQueryLog();
			$user = User::whereEmail($request->email)->first();
			if (isset($user) && $user->active)
			{
				$f = Hash::check($request->password, $user->password);

				if($f == True)
				{
					$this->code ='0011';
					$this->message = Common::code($this->code);
				}
				else 
				{
					$this->code = '0019';
					$this->message = Common::code($this->code);
				}
			}
			elseif(isset($user) && !$user->active)
			{	
				
				$this->code = '0031';
				$this->message = Common::code($this->code);
				$user = Null;

			}
			else
			{
				
				$this->code = '0022';
				$this->message = Common::code($this->code);
			}

			//$queries = DB::getQueryLog();
			//return response()->json(['status' => $queries]);
		}
		else
		{
			$this->code = '004';
			$this->message = $validator->errors();
		}
		return response()->json(['status' => $this->code, 'message' => $this->message, 'data' => $user]);
	}


	public function activate($token)
	{
		$user = User::whereActivationToken($token)->first();
		if(!isset($user))
		{
			$this->code = '0052';
			$this->message= 'Invalid token';
		}
		else
		{
			$user->active = 1;
			$user->activation_token = null;
			$user->save();
			
			$this->code = '0018';
			$this->message = Common::code($this->code);
		}
		return response()->json(['status' => $this->code, 'message' => $this->message]);
	}

	public function update(Request $request)
	{

	}

	public function forgotPasswordMail(Request $request)
	{

	}



}
