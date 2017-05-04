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

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="roomrent.dev",
 *     basePath="/api",
 *     @SWG\Info(
 *         title="Roomrent documentation API",
 *         version="1.0.0"
 *         )
 * )
 */
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
    
    /**
     * @SWG\Post(
     *     path="/register",
     *     tags={"user"},
     *     summary="create new user",
     *     description="available for new user",
     *     operationId="createUser",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="formData",
     *         name="email",
     *         format="string",
     *         description="email",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="username",
     *         description="username",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="name",
     *         description="Name",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="password",
     *         description="password",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="phone",
     *         description="Phone No.",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(response="405", description="Invalid inputs")
     * )
     */
  
    public function store(RegisterRequest $request)
    {
        $user = $request->only([
            'username','email','phone','name'
        ]);

        $user['password']           = Hash::make($request->password);
        $user['activation_token']   = str_random(60);
        
        if ($request->file('file')) {
            $user['profileImage']  =
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
    
    /**
     * @SWG\Post(
     *     path="/login",
     *     tags={"user"},
     *     summary="login a user",
     *     description="User must be registered",
     *     operationId="loginUser",
     *     consumes="multipart/form-data",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="formData",
     *         name="identity",
     *         description="username or email",
     *         required=true,
     *         type="string"
     *         ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="password",
     *         description="password of user",
     *         required=true,
     *         type="string"
     *         ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="device_type",
     *         description="type of device",
     *         required=true,
     *         type="string"
     *         ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="device_token",
     *         description="device specific token",
     *         required=true,
     *         type="strin"
     *         ),
     *     @SWG\Response(response="400", description="Invalid username or password")
     * )
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
    /**
     * @SWG\Get(
     *     path="/activate/{token}",
     *     tags={"user"},
     *     summary="activates the user",
     *     description="user need to be registered",
     *     operationId="activate",
     *     produces="application/json",
     *     @SWG\Parameter(
     *         in="path",
     *         name="token",
     *         description="activate user with the token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(response="405", description="invalid token")
     * )
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
    
    /**
     *@SWG\Post(
     *  path="/forgotpassword",
     *  tags={"user"},
     *  summary="mails for new password",
     *  description="only for already registered user",
     *  operationId="mailForgotPassword",
     *  produces="application/json",
     *  @SWG\Parameter(
     *      in="formData",
     *      name="email",
     *      description="Email that user registered with",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Response(response="405", description="invalid inputs")
     *)
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
     /**
     * @SWG\Get(
     *     path="/forgotpassword/{token}",
     *     tags={"user"},
     *     summary="check token and display form for password reset",
     *     description="user need to be registered",
     *     operationId="tokenCheckForgotPassword",
     *     produces="application/json",
     *     @SWG\Parameter(
     *         in="path",
     *         name="token",
     *         description="check the token for password reset",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(response="405", description="invalid token")
     * )
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
     
    /**
     *@SWG\Post(
     *  path="/forgotpassword/change",
     *  tags={"user"},
     *  summary="change password of user",
     *  description="only registered user can change the password",
     *  operationId="forgotPasswordChange",
     *  produces="application/json",
     *   @SWG\Parameter(
     *      in="formData",
     *      name="email",
     *      description="email",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Parameter(
     *      in="formData",
     *      name="newPassword",
     *      description="new password to be set",
     *      required=true,
     *      type="string"
     *  ),
     *   @SWG\Parameter(
     *      in="formData",
     *      name="newPassword_confirmation",
     *      description="user whose password to be changed",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Response(response="405", description="invalid inputs")
     *)
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
    
     /**
     *@SWG\Post(
     *  path="/changepassword",
     *  tags={"user"},
     *  summary="change password of loggedin user",
     *  description="only loggedin user can change password",
     *  operationId="changePassword",
     *  produces="application/json",
     *   @SWG\Parameter(
     *      in="formData",
     *      name="oldPassword",
     *      description="old password",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Parameter(
     *      in="formData",
     *      name="newPassword",
     *      description="new password to be set",
     *      required=true,
     *      type="string"
     *  ),
     *  security={
     *      {"api_key":{}}
     *  },
     *  @SWG\Response(response="405", description="invalid inputs")
     *)
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
    /**
     * @SWG\Post(
     *     path="/logout",
     *     tags={"user"},
     *     summary="logout",
     *     operationId="logout",
     *     produces="application/json",
     *     description="user must be logged in",
     *     parameters={},
     *     security={
     *         {"api_key":{}}
     *     },
     *     @SWG\Response(response="default", description="successfully logout")
     * )
     */
    public function logout(Request $request)
    {
        $code = auth()->user()->delete()? '0020' : '0052';
        
        return response($this->helper->userResponse(['code' =>  $code]));
    }
}
