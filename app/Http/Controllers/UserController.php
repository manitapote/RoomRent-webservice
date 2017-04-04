<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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

			//Mail::to($user)->send(new ActivationEmail);

			return response()->json(['status' => '200',
				'message' => 'Registered' ]);
		}
		else
			return response()->json(['status' => '006','data' => $validator->errors()]);
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
					$this->code ='002';
					$message = "login sucess";
				}
				else 
				{
					$message = "password incorrect";
					$this->code = '003';
				}
			}
			elseif(isset($user) && !$user->active)
			{	
				$message = "Email not active";
				$this->code = '006';
				$user = Null;

			}
			else
			{
				$message = "Email not found";
				$this->code = '005';
			}
			//$queries = DB::getQueryLog();
			//return response()->json(['status' => $queries]);
		}
		else
		{
			$this->code = '004';
			$message = $validator->errors();
		}
		return response()->json(['status' => $this->code, 'message' => $message, 'data' => $user]);
	}





}
