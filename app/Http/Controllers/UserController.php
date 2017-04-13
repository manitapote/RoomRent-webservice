<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use Validator;
use App\Common;
use App\Mail\ForgotPasswordEmail;
use App\Mail\ActivationEmail;   
use App\User;
use App\Device;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;

class UserController extends Controller
{
    public    $response = [];
    protected $codeMessage;
    protected $user;
    protected $mail;
    protected $device;

    const ACTIVE   = 1;
    const INACTIVE = 0;

    public function __construct(Common $codeMessage, User $user, Device $device)
    {
        $this->codeMessage = $codeMessage;
        $this->user        = $user;
        $this->device = $device;
        $this->httpStatus = 200;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'name'     => 'alpha',
            'password' => 'required',
            'phone'    => 'numeric',
            'username' => 'required|min:5|max:35|unique:users,username',
            ]);

        if (!$validator->fails()) {
            if ($request->file) {
                $tt   = $request->file;
                $path = base_path().'/resources/profileImages';

                $binary = base64_decode($tt);
                $time   = $path.'/'.time().'.jpg';
                $file   = fopen($time, 'wb');
                fwrite($file, $binary);
                fclose($file);
                $this->user->profileImageURL = $time;
            }

            $this->user->username         = $request->username;
            $this->user->password         = Hash::make($request->password);
            $this->user->email            = $request->email;
            $this->user->name             = $request->name ? $request->name : null;
            $this->user->phone            = $request->phone ? $request->phone : null;
            $this->user->activation_token = str_random(60);

            $this->user->save();

            Mail::to($this->user)->send(new activationEmail($this->user));
            $response = [
            'code' => '0013',
            ];
            $this->httpStatus = 201;
        } else {
            $this->httpStatus = 400;
            $response = [
            'errors' => $validator->errors(),
            'code'   => '0014',
            'data'   => $request->all(),
            ];
        }
        
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response, $this->httpStatus);
    }

    public function login(LoginRequest $request)
    {
        $loginError = true;
        $field = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user   = $this->user->where($field, $request->identity)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $loginError = false;

                if (!$user->active) {
                    $response = [
                    'uses' => $field,
                    'code' => '0031',
                    ];
                    $this->httpStatus = '401';
                } else{
                    $user->update(['forgot_token' => null]);
                    $request->merge(array('user_id' => $user->id));
                    $device = $this->user->storeDevice($request);
                    $response = [
                    'uses' => $field,
                    'user' => $user,
                    'code' => '0011',
                    'api_token' => $device->api_token,
                    ];
                }
            }
        }
        if ($loginError) {
            $response = [
            'uses' => $field,
            'code' => '0012',
            ];
        }

        $response['message'] = sprintf($this->codeMessage->code($response['code']), $field);

        return response($response, $this->httpStatus);
    }


    public function activate($token)
    {
        $user = $this->user->whereActivationToken($token)->update([
            'active' => self::ACTIVE,
            'activation_token' => null,
            ]);
        
        $response['code'] = isset($user) ? '0015' : '0052';
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    public function update(Request $request)
    {

    }

    public function mailForgotPassword(Request $request)
    {
        $user = $this->user->whereEmail($request->email)->first();

        if ($user) {
            $user->forgot_token = str_random(60);
            $user->save();
            Mail::to($user)->send(new ForgotPasswordEmail($user));
            $response['code'] = "0023";
        } else {
            $response['code'] = "0022";
        }

        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    public function tokenCheckForgotPassword($token = null)
    {
        $user = $this->user->whereForgotToken($token)->first();

        if ($token != null && $user) {
            $error = '';
            $user->update([
                'forgot_token' => null
                ]);
            $email = $user->email;

            return view('forgotPasswordForm', compact('email', 'error'));
        }

        $response['code'] = '0052';
        $response['message'] = $this->codeMessage->code($response['code']);
        return response($response);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newPassword' => 'required|confirmed',
            ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $email = $request->email;

            return view('forgotPasswordForm', compact('email', 'error'));
        } else {
            $user           = $this->user->whereEmail($request->email)->first();
            $user->update([
                'password' => Hash::make($request->newPassword)
                ]);

            $response['code']    = '0024';
            $response['message'] = $this->codeMessage->code($response['code']);
        }
        return response($response['message']);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $device = $this->device->whereApiToken($request->api_token)->first();

        if ($device && ($user =  $device->user)) {
            if (Hash::check($request->oldPassword, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->newPassword)
                ]);
                $response ['code'] = '0001';
            } else
            $response ['code'] = '0021';
        } else
        $response = [
        'code' => '0032',
        ];
        $response['message'] = $this->codeMessage->code($response['code']);
        return response($response);
    }

    public function logout(Request $request)
    {
        $response['code'] = $this->device->whereApiToken($request->api_token)->delete() ? '0020' : '0052';
        $response['message'] = $this->codeMessage->code($response['code']);
        return response($response);
    }
}
