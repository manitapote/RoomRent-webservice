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
    * @param ResponseHelper $responseHelper 
    */
    public function __construct(
        UserService $userService,
        ResponseHelper $responseHelper)
    {
        $this->userservice    = $userService;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Register the user in users table
     * 
     * @param  RegisterRequest $request
     * @return json
     * 
     */
    public function store(RegisterRequest $request)
    {
        $user = $this->userservice->getUserDataFromRequest($request);
        
        return $this->userservice->registerOrUpdate($user);
    }

    /**
     * Login the user
     * 
     * @param  LoginRequest $request
     * @return json
     */
    public function login(LoginRequest $request)
    {
        $field = filter_var($request->identity, FILTER_VALIDATE_EMAIL)
            ? 'email' : 'username';
        $user  = $this->userservice->findBy($field, $request->identity);
        
        $user['profileImage'] = url('/api/image')."/".$user['profileImage'];

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
     */
    public function activate($token)
    {
        $user = $this->userservice->findBy('activation_token', $token);

        return $this->userservice->activateUser($user);
    }

    /**
     * Mail to user with forgot password token
     * 
     * @param  Request
     * @return json
     * 
     */
    public function mailForgotPassword(Request $request)
    {
        $user = $this->userservice->findBy('email',$request->email);

        if (!$user) {
            return response($this->responseHelper->jsonResponse(
             ['code' => '0022']));
        }

        $this->userservice->update($user, ['forgot_token' => str_random(60)]);

        Mail::to($user)->send(new ForgotPasswordEmail($user));
      
        return response($this->responseHelper->jsonResponse(['code' => '0023']));
    }

    /**
     * checks the forgot token from mail with forgot_token in users table
     *      
     * @param  string|null
     * @return html_form|json
     * 
     */
    public function tokenCheckForgotPassword($token = null)
    {
        $user = $this->userservice->findBy('forgot_token', $token);

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
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = $this->userservice->findBy('forgot_token', $request->token);

        if(!$user)

            return response($this->responseHelper->jsonResponse([
             'code' => '0051']));

        $this->userservice->update($user, [
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
         
        $this->userservice->update(
            $user,
            ['password' => Hash::make($request->newPassword)]);
              
        return response($this->responseHelper->jsonResponse(['code' => '0001'], 'changed'));
    }

    /**
     * Logout the user by deleting the associated api_token from devices table
     * @param  Request
     * @return json
     */
    public function logout()
    {
        $code = $this->userservice->updateDeviceInfo(
            auth()->user(), ['api_token' => null])? 
            '0020' : '0052';
        
        return response($this->responseHelper->jsonResponse(['code' =>  $code]));
    }

    /**
     * Gets info of particuler User
     * @param  Integer $id 
     * @return JSON
     */
    public function getParticulerUser($id)
    {
        $user = $this->userservice->findBy('id', $id);
        
        if (!$user) {
            return response($this->responseHelper->jsonResponse(['code' => '0081', 'user' => $user]));

        }

        $user['profileImage'] = url('/api/image'."/".$user['profileImage']);
        return response($this->responseHelper->jsonResponse(['code' => '0091', 'user' => $user]));

    }
}
