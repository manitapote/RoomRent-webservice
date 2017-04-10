<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mail;
use Validator;
use App\Common;
use App\Mail\ActivationEmail;
use App\Mail\ForgotPasswordEmail;
use App\User;

class UserController extends Controller
{
	public $response = array();

	public function store(Request $request)
	{
		$path ='';
		$user = new User;
		//$error = '';

		$validator = Validator::make($request->all(), [
			'email' => 'required|email|unique:users,email',
			'name' => 'alpha',
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
		$field = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		$user = User::where($field, $request->identity)->first();

		if ($user && ($user->api_token == Null)) {
			if (Hash::check($request->password, $user->password)) {
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
			$error = '';
			//$user->forgot_token = Null;
			$user->save();
			return view('forgotPasswordForm', compact('user','error'));
		}
		else
			$response['status'] = '0052';

		$response['message'] =  Common::code($response['status']);
		return response($response);
	}

	public function resetPassword( Request $request)
	{
		$u = new User();
		$choice = ($request->identity == 'email')? 1 : 2;
		$validator = Validator::make($request->all(),$u->getValidationRules($choice));
		if($validator->fails() && !$request->email){
			$response = [
			'status' => '0014',
			'errors' => $validator->errors(),
			];
			$response['message'] = Common::code($response['status']);
			return response($response);
		}

		$field = $request->identity;
		$user = User::where($field, $request->value)->first();
		
		if(!isset($user)){
			$response['status'] = ($field == 'api_token')? '0052' :'0022';
			$response['message'] = Common::code($response['status']);
			return response($response);
		}

		if(($field == 'api_token') && !Hash::check($request->oldPassword, $user->password)){
			$response['status'] = '0021';
		}else {
			$response['status'] = '0001';
			$user->password = Hash::make($request->newPassword);
			$user->save();
		}

		$response['message'] = Common::code($response['status']);

		if($request->identity == 'email'){
			if($validator->fails()){
				$error = $validator->errors();
				return view ('forgotPasswordForm', compact('error, user'));
			}
			else
				return ("Password Changed Successfully");
			
			return response($response);
		}
	}

	public function logout(Request $request)
	{
		if($user = User::whereApiToken($request->api_token)->first()){
			$user->update(['api_token' => Null]);
			$response['status'] = '0001';
		}else
		$response['status'] = '0052';
		$response['message'] = Common::code($response['status']);
		return response($response);
	}
}

