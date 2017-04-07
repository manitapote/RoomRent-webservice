<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
//use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Mail;
use Validator;

use App\Common;
use App\Mail\ActivationEmail;
use App\Mail\ForgotPasswordEmail;
use App\User;

class UserController extends Controller 
{
	// public function __construct()
	// {
	// 	$this->middleware('reset_password',['only' => 'reset_password']);
	// }

	public $response = array();

	public function store(Request $request)
	{
		$path ='';
		$user = new User;
		$error = '';

		$validator = Validator::make($request->all(), [
			'email' => 'required|email|unique:users,email',
			//'name' => 'alphabetic',
			'password' => 'required',
			'phone' => 'numeric',
			'username' => 'required|min:5|max:35|unique:users,username',
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

			$user->username = $request->username;
			$user->password = Hash::make($request->password);
			$user->email = $request->email;
			$user->name = $request->name ? $request->name : Null;
			$user->phone = $request->phone ? $request->phone : Null;
			$user->activation_token = str_random(60);

			$user->save();

			Mail::to($user)->send(new activationEmail($user));
			$response = [
			'user' => $user,
			'code' => '0013'
			];
		}
		else
		{
			$response = [
			'errors' => $validator->errors(),
			'code' => '0016'
			];
		}

		$response['message'] = Common::code($response['code']);

		return response($response);
	}


	public function login( Request $request)
	{
		$loginError = true;
		$field = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		$user = User::where($field, $request->identity)->first();

		if ($user) {
			if (Hash::check($request->password, $user->password)) {
				$loginError = false;
				if (!$user->active) {
					$response = [
					'uses' => $field,
					'user' => $user,
					'code' => '0031'
					];
				}else{
					$user->api_token = str_random(60);
					$user->forgot_token = null;
					$user->save();
					$response = [
					'uses' => $field,
					'user' => $user,
					'code' => '0011'
					];
				}
			}else{
				$response = [
				'code' => '0019'
				];
			}

		}else {
			$response = [
			'uses' => $field,
			'code' => '0012'
			];
		}
			//$response['message'] = sprintf(Common::code($response['code']), $field);
		$response['message'] = Common::code($response['code']);
		return response($response);

	} 

	
	public function activate($token)
	{
		$user = User::whereActivationToken($token)->first();
		if(!isset($user))
		{
			$response['status'] = '0052';
		}else{

			$user->active = 1;
			$user->activation_token = null;
			$user->save();

			$response['status'] = '0018';
		}

		$response['message'] = Common::code($response['status']);
		return response($response); 
	}

	public function update(Request $request)
	{

	}

	public function mailForgotPassword(Request $request)
	{
		$user = User::whereEmail($request->email)->first();

		if($user){ 
			$user->forgot_token = str_random(60);
			$user->save();
			Mail::to($user)->send(new ForgotPasswordEmail($user));
			$response['status'] = "0023";
		}else {
			$response['status'] = "0022";
		}

		$response['message'] = Common::code($response['status']);
		return response($response);
	}


	public function tokenCheckForgotPassword($token = Null)
	{
		$user = User::whereForgotToken($token)->first();
		if($user){
			$user->forgot_token = Null;
			$user->save();
			$response['status'] = '0002';
		}
		else
			$response['status'] = '0052';

		$response['message'] =  Common::code($response['status']);
		return response($response);
	}

	public function resetPassword( Request $request)
	{
		//return $request->identity;
		$u = new User();
		$field = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'api_token';
		//return $field;
		$choice = ($field == 'email')? 1 : 2;
		//return $choice;
		//return $u->getValidationRules($choice);
		$validator = Validator::make($request->all(),$u->getValidationRules($choice));
		//return $validator->errors();
		if($validator->fails()){
			$response = ['status' => '0014', 'errors' => $validator->errors()];
			$response['message'] = Common::code($response['status']);
			return response($response);
		}

		$user = User::where($field, $request->identity)->first();
		//return $user;
		if(!isset($user)){
			$response['status'] = ($field == 'api_token')? '0052' :'0022';
			$response['message'] = Common::code($response['status']);
			return response($response);
		}

		if(($field == 'api_token') && !Hash::check($request->oldPassword, $user->password))
			$response['status'] = '0021';
		
		$user->password = Hash::make($request->oldPassword);
		$user->save();
		$response['status'] = '0001';

		$response['message'] = Common::code($response['status']);
		return response($response);
	}
}

