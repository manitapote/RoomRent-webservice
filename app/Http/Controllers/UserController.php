<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\ForgotPasswordEmail;
use App\Mail\ActivationEmail;   
use App\User;
use App\Device;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use ImageHelper;
use Mail;
use Validator;

class UserController extends Controller
{
    /**
     * Object to bind to Helper class
     * @var [type]
     */
    public $helper;

    /**
     * stores response to request
     * @var array
     */
    public $response = [];

    /**
     * Object to bind to User model
     * @var User
     */
    protected $user;
    
    /**
     * Object to bind to Device model
     * @var Device
     */
    protected $device;

    /**
     * @param User    $user
     * @param Device  $device
     */
    public function __construct(User $user, Device $device, Helper $helper)
    {
        $this->helper = $helper;
        $this->user   = $user;
        $this->device = $device;
    }

    /**
     * Register the user in users table
     * 
     * @param  RegisterRequest $request
     * @return json
     */
    public function store(RegisterRequest $request)
    {
        $user = $request->only([
            'username','email','phone','name'
        ]);

        $user['password']           = Hash::make($request->password);
        $user['activation_token']   = str_random(60);
        
        if ($request->file('file')) {
            $user['profileImageURL']  =
                ImageHelper::saveImage($request->file, config(
                    'constants.PROFILE_IMAGE_FOLDER'
                ));
        }

        $user = $this->user->create($user);

        Mail::to($user)->send(new activationEmail($user));
       
        return response($this->helper->userResponse(['code' => '0013']));
    }

    /**
     * Login the user and save the device info to devices table
     * 
     * @param  LoginRequest $request
     * @return json
     */
    public function login(LoginRequest $request)
    {
        $field  = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
            ? 'email' : 'username';

        $user = $this->user->where($field, $request->identity)->first();

        if(!($user && Hash::check($request->password, $user->password))) {
            $response = $this->helper->userResponse(['code' => '0012']);

            $response['message'] = sprintf($response['message'], $field);

            return response($response);
        }
        
        if (!$user->active)

            return response($this->helper->userResponse([
                'code' =>'0031',
                'uses' => $field
            ]));
            
        $user->update(['forgot_token' => null]);
        $request->merge(array('user_id' => $user->id));

        $device   = $this->user->storeDevice($request);

        return response($this->helper->userResponse([
            'code'      => '0011', 
            'uses'      => $field, 
            'user'      => $user,
            'api_token' => $device->api_token
        ]));
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
            'active' => config('constants.ACTIVE'),
            'activation_token' => null,
        ]);

        $code = ($user) ? '0015' : '0052';
        
        return response($this->helper->userResponse(['code' => $code]));
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

        if (!$user) {
            return response($this->helper->userResponse(['code' => '0022']));
        }

        $user->update(['forgot_token' => str_random(60)]);

        Mail::to($user)->send(new ForgotPasswordEmail($user));
      
        return response($this->helper->userResponse(['code' => '0023']));
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

        if (!($token != null && $user))
            return response($this->helper->userResponse(['code' => '0052']));
        
        $error = '';
        $user->update(['forgot_token' => null]);
        $email = $user->email;

        return view('forgotPasswordForm', compact("email", 'error'));
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
        }

        $this->user->whereEmail($request->email)
            ->update(['password' => Hash::make($request->newPassword)]);

        return response($this->helper->userResponse(['code' => '0024']));
    }

    /**
     * checks if post field oldPassword match with password in users table and 
     * saves new password
     * 
     * @param  ResetPasswordRequest
     * @return json
     */
    public function changePassword(ResetPasswordRequest $request)
    {
        $device = auth()->user();
        $user   =  $device->user;

        if (!Hash::check($request->oldPassword, $user->password)) {
            return response($this->helper->userResponse(['code' => '0021']));
        }
         
        $user->update(['password' => Hash::make($request->newPassword)]);
              
        return response($this->helper->userResponse(['code' => '0001']));
    }

    /**
     * Logout the user by deleting the associated api_token from devices table
     * @param  Request
     * @return json
     */
    public function logout(Request $request)
    {
        $code = $this->device
            ->whereApiToken($request->api_token)
            ->delete() ? '0020' : '0052';
        
        return response($this->helper->userResponse(['code' =>  $code]));
    }
}
