<?php

namespace Roomrent\User\Controller;

use Roomrent\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Roomrent\Helpers\ResponseHelper;
use Roomrent\Mail\ForgotPasswordEmail;
use Roomrent\User\Services\UserService;
use Roomrent\User\Requests\LoginRequest;
use Roomrent\User\Requests\RegisterRequest;
use Roomrent\User\Requests\ResetPasswordRequest;
use Roomrent\User\Requests\ForgotPasswordRequest;
use Roomrent\User\Repositories\UserRepositoryInterface;
use Auth;
use Mail;

class UserController extends ApiController
{
    /**
     * Object to bind to ResponseHelper class
     * @var [type]
     */
    public $responseHelper;

    /**
     * Object to bind to UserRepository
     * @var Device
     */
    protected $user;  

    /**
     * Object to bind to UserService class
     * @var UserService
     */
    protected $userService;


   /**
    * Constructor
    * @param UserService    $userService    
    * @param UserRepository $user         
    * @param ResponseHelper $responseHelper 
    */
    public function __construct(
        UserService $userService,
        UserRepositoryInterface $user,
        ResponseHelper $responseHelper)
    {
        $this->user           = $user;
        $this->userservice    = $userService;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Register the user in users table
     * 
     * @param  RegisterRequest $request
     * @return json
     * 
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
        $user = $this->userservice->getUserDataFromRequest($request);
        $user = $this->user->createUser($user);

        return $this->userservice->checkUserAndMail($user);
    }

    /**
     * Login the user
     * 
     * @param  LoginRequest $request
     * @return json
     * 
     * @SWG\Post(
     *     path="/login",
     *     tags={"user"},
     *     summary="login a user",
     *     description="User must be registered",
     *     operationId="loginUser",
     *     consumes={"application/x-www-form-urlencoded"},
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
     *         type="string"
     *         ),
     *     @SWG\Response(response="400", description="Invalid username or password")
     * )
     */
    public function login(LoginRequest $request)
    {
        $field = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
            ? 'email' : 'username';

        $user  = $this->user->getUserByField($field, $request->identity);
        
        return ($this->userservice->checkCredentialAndLogin(
            $user, 
            $request,
            $field)); 
    }

    /**
     *Activates the user 
     * 
     * @param  string $token 
     * @return json
     * 
     * @SWG\Get(
     *     path="/activate/{token}",
     *     tags={"user"},
     *     summary="activates the user",
     *     description="user need to be registered",
     *     operationId="activate",
     *     produces={"application/json"},
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
        $user = $this->user->getUserByField('activation_token', $token);

        return $this->userservice->activateUser($user);
    }

    /**
     * Mail to user with forgot password token
     * 
     * @param  Request
     * @return json
     * 
     *@SWG\Post(
     *  path="/forgotpassword",
     *  tags={"user"},
     *  summary="mails for new password",
     *  description="only for already registered user",
     *  operationId="mailForgotPassword",
     *  produces={"application/json"},
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
        $user = $this->user->getUserByField('email',$request->email);

        if (!$user) {
            return response($this->responseHelper->jsonResponse(
             ['code' => '0022']));
        }

        $this->user->updateUser($user, ['forgot_token' => str_random(60)]);

        Mail::to($user)->send(new ForgotPasswordEmail($user));
      
        return response($this->responseHelper->jsonResponse(['code' => '0023']));
    }

    /**
     * checks the forgot token from mail with forgot_token in users table
     *      
     * @param  string|null
     * @return html_form|json
     * 
     * @SWG\Get(
     *     path="/forgotpassword/{token}",
     *     tags={"user"},
     *     summary="check token and display form for password reset",
     *     description="user need to be registered",
     *     operationId="tokenCheckForgotPassword",
     *     produces={"application/json"},
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
        $user = $this->user->getUserByField('forgot_token', $token);

        if (!($token != null && $user))
            return response($this->responseHelper->jsonResponse(
             ['code' => '0052']));
        
        $data['token'] = $token;
        $data['error'] = "";

        return view('forgotPasswordForm', compact("data"));
    }

    /**
     * validates the form post and saves new password
     * 
     * @param  ForgotPasswordRequest
     * @return json
     * 
     *@SWG\Post(
     *  path="/forgotpassword/change",
     *  tags={"user"},
     *  summary="change password of user",
     *  description="only registered user can change the password",
     *  operationId="forgotPasswordChange",
     *  produces={"application/json"},
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
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = $this->user->getUserByField('forgot_token', $request->token);

        if(!$user)

            return response($this->responseHelper->jsonResponse([
             'code' => '0051']));

        $this->user->updateUser($user, [
            'forgot_token' => null,
            'password' =>  Hash::make($request->newPassword)
            ]);
       
        return response($this->responseHelper->jsonResponse(['code' => '0024']));
    }

    /**
     * checks if post field oldPassword match with password in users table and 
     * saves new password
     * 
     * @param  ResetPasswordRequest
     * @return json
     * 
     *@SWG\Post(
     *  path="/changepassword",
     *  tags={"user"},
     *  summary="change password of loggedin user",
     *  description="only loggedin user can change password",
     *  operationId="changePassword",
     *  produces={"application/json"},
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
        $deviceInfo = auth()->user();
        $user       =  $deviceInfo->user;

        if (!$this->userservice->checkPassword(
            $request->oldPassword,
            $user->password)) {
            return response($this->responseHelper->jsonResponse(
             ['code' => '0021']));
        }
         
        $this->user->updateUser(
            $user,
            ['password' => Hash::make($request->newPassword)]);
              
        return response($this->responseHelper->jsonResponse(['code' => '0001']));
    }

    /**
     * Logout the user by deleting the associated api_token from devices table
     * @param  Request
     * @return json
     * 
     * @SWG\Post(
     *     path="/logout",
     *     tags={"user"},
     *     summary="logout",
     *     operationId="logout",
     *     produces={"application/json"},
     *     description="user must be logged in",
     *     parameters={},
     *     security={
     *         {"api_key":{}}
     *     },
     *     @SWG\Response(response="default", description="successfully logout")
     * )
     */
    public function logout()
    {
        $code = $this->user->updateDeviceInfo(
            auth()->user(), ['api_token' => null])? 
            '0020' : '0052';
        
        return response($this->responseHelper->jsonResponse(['code' =>  $code]));
    }
}
