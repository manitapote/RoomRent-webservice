<?php
namespace App\Http\Controllers;

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
use App\Http\Requests\RegisterRequest;
use Helper;

class UserController extends Controller
{
    /**
     * stores response to request
     * @var array
     */
    public    $response = [];

    /**
     * object to bind to Common class
     * @var Common
     */
    protected $codeMessage;

    /**
     * object to bind to User model
     * @var User
     */
    protected $user;
    
    /**
     * object to bind to Device model
     * @var Device
     */
    protected $device;

    /**
     * constant to denote active and inactive status
     */
    const ACTIVE   = 1;
    const INACTIVE = 0;

    /**
     * @param Common  $codeMessage
     * @param User    $user
     * @param Device  $device
     */
    public function __construct(Common $codeMessage, User $user, Device $device)
    {
        $this->codeMessage = $codeMessage;
        $this->user        = $user;
        $this->device      = $device;
    }

    /**
     * Register the user in users table
     * 
     * @param  RegisterRequest $request
     * @return json
     */
    public function store(RegisterRequest $request)
    {
        $user                     = $request->only([
                                    'username','email','phone','name'
                                    ]);
        $user['password']         = Hash::make($request->password);
        $user['activation_token'] = str_random(60);
        
        if($request->file){
            $path                     = base_path()."/resources/profileImages";
            $user['profileImageURL']  = $path.'/'.Helper::saveImage(
                                        $request->file, $path);
        }
        $user = $this->user->create($user);

        Mail::to($user)->send(new activationEmail($user));
        $response            = ['code' => '0013'];
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    /**
     * Login the user and save the device info to devices table
     * 
     * @param  LoginRequest $request
     * @return json
     */
    public function login(LoginRequest $request)
    {
        $loginError = true;
        $field      = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ?
                      'email' : 'username';
        $user       = $this->user->where($field, $request->identity)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $loginError = false;

                if (!$user->active) {
                    $response = [
                                'uses' => $field,
                                'code' => '0031',
                                ];
                } else{
                    $user->update(['forgot_token' => null]);
                    $request->merge(array('user_id' => $user->id));
                    $device   = $this->user->storeDevice($request);
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

        $response['message'] = sprintf(
                               $this->codeMessage->code(
                               $response['code']), $field);

        return response($response);
    }

    /**
     * checks the token parameter with api_token in devices table
     * 
     * @param  string $token 
     * @return json
     */
    public function activate($token)
    {
        $user = $this->user->whereActivationToken($token)->update([
                'active' => self::ACTIVE,
                'activation_token' => null,
                ]);

        $response['code']    = ($user) ? '0015' : '0052';
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    public function update(Request $request)
    {

    }

    /**
     * Mail to user with forgot password token
     * 
     * @param  Request
     * @return json
     */
    public function mailForgotPassword(Request $request)
    {
        $user = $this->user->whereEmail($request->email)->first();

        if ($user) {
            $user->update(['forgot_token' => str_random(60)]);
            Mail::to($user)->send(new ForgotPasswordEmail($user));
            $response['code'] = "0023";
        } else {
            $response['code'] = "0022";
        }

        $response['message']  = $this->codeMessage->code($response['code']);

        return response($response);
    }

    /**
     * checks the forgot token from mail with forgot_token in users table
     *      
     * @param  string|null
     * @return html_form|json
     */
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

        $response['code']    = '0052';
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    /**
     * validates the form post and saves new password
     * 
     * @param  Request
     * @return json
     */
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

    /**
     * checks if post field oldPassword match with password in users table and 
     * saves new password
     * 
     * @param  ResetPasswordRequest
     * @return json
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $device = Auth::guard('api')->user();

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

    /**
     * Logout the user by deleting the associated api_token from devices table
     * @param  Request
     * @return json
     */
    public function logout(Request $request)
    {
        $response['code']    = $this->device
                             ->whereApiToken($request->api_token)
                             ->delete() ? '0020' : '0052';
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }
}
