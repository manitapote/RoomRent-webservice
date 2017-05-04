<?php

namespace Roomrent\User\Services;

use Illuminate\Support\Facades\Hash;
use Roomrent\Helpers\ResponseHelper;
use Roomrent\Helpers\ImageHelper;
use Roomrent\User\Repositories\UserRepositoryInterface;
use Roomrent\Mail\ActivationEmail;   
use Auth;

use Mail;


class UserService
{
  /**
   * Object to bind to UserRepositoryInterface
   * @var User
   */
	protected $user;

  /**
   * Object to bind to ResponseHelper class
   * @var Object
   * 
  */
  protected $responseHelper;

  /**
   * Object to bind to ImageHelper
   * @var Object
   */
  protected $imageHelper;

  /**
   * Constructor
   * @param User           $user           
   * @param Device         $device         
   * @param ResponseHelper $responseHelper 
   */
	public function __construct(
    UserRepositoryInterface $user,
    ResponseHelper $responseHelper,
    ImageHelper $imageHelper)
	{
		$this->user           = $user;
    $this->responseHelper = $responseHelper;
    $this->imageHelper    = $imageHelper;
	}

  /**
   * Gets the user data from $request, saves image if present
   * 
   * @param  RegisterRequest $request 
   * @return Array
   */
	public function getUserDataFromRequest($request)
	{
    $user = $request->only([
        'username','phone','name','file'
      ]);

    if (auth()->user()) {
      return $user;
    }

    $user['email'] = $request->email;
    $user['password']         = Hash::make($request->password);
    $user['activation_token'] = str_random(60);

    return $user;
	}

  public function handleFileFromRequest($user)
  {
    if ($user['file']) {
      $user['profileImage'] = $this->imageHelper
        ->saveImage($user['file'], config('constants.PROFILE_IMAGE_FOLDER'
      ));
    }

    return $user;
  }
 
  /**
   * Register or Update the user on the basis of coming url
   * @param  Array $user 
   * @return JSON
   */
  public function registerOrUpdate($user)
  {
    if (auth()->user()) {
      $updateUser  = auth()->user()->user;
      $userPresent = $this->findBy('username', $user['username']);
      if ($userPresent) {
        return response($this->responseHelper->jsonResponse([
          'code' => '0082'],'username'));
      }

      $user = $this->handleFileFromRequest($user);

      if ($this->update($updateUser, $user)) {
        return response($this->responseHelper->jsonResponse([
          'code' => '0001'],'updated'));
      }

      return responseHelper($this->responseHelper->jsonResponse([
        'code' => '0000']));
    }

    $user = $this->handleFileFromRequest($user);

    $user = $this->create($user);

    return $this->checkUserAndMail($user);
  }

  /**
   * Checks if user is present, active and password matches,
   * if matches saves device info posted
   * 
   * @param  User         $user    
   * @param  LoginRequest $request 
   * @param  String       $field   Either email or username
   * @return JSON          
   */
  public function checkCredentialAndLogin($user, $request, $field)
  {
    if(!$user || !($user && $this->checkPassword(
      $request->password,
      $user->password))) {
           
      return $this->responseHelper->jsonResponse(
          ['code' => '0012'],
          $field);
    }

    if (!$user->active) {
      return $this->responseHelper->jsonResponse([
          'code' =>'0031',
          'uses' => $field
      ]);
    }

    $this->user->update($user, ['forgot_token' => null]);

    $deviceInfo = $this->user->storeDeviceInfo($request, $user->id);

    return response($this->responseHelper->jsonResponse([
      'code'      => '0011', 
      'uses'      => $field, 
      'user'      => $user,
      'api_token' => $deviceInfo->api_token
      ]));
  }

  /**
   * Checks the input password with password saved
   * 
   * @param  String $inputPassword 
   * @param  String $userPassword
   * @return Boolean
   */
  public function checkPassword($inputPassword, $userPassword)
  {
    return (Hash::check($inputPassword, $userPassword));
  }

  /**
   * Activates the user
   * 
   * @param  String $token 
   * @return Boolean        
   */
  public function activateUser($user)
  {
    $update = "";
   
    if ($user) {
      $update = $this->user->update(
       $user,[
       'active' => config('constants.ACTIVE'),
        'activation_token' => null,
       ]);
    }

    $code = ($update) ? '0015' : '0052';

    return response($this->responseHelper->jsonResponse(['code' => $code]));
  }

  /**
   * Checks if User is present and sends mail to user
   * @param  Object $user 
   * @return JSON
   */
  public function checkUserAndMail($user)
  {
    if (!$user) {
         return response($this->responseHelper->jsonResponse(
          ['code' => '0081']));
    }

    Mail::to($user)->send(new activationEmail($user));

    return response($this->responseHelper->jsonResponse(['code' => '0013']));

  }

  /**
   * Creates new record
   * @param  Array $user  binds to create function in Repository
   * @return Object
   */
  public function create($user)
  {
    return $this->user->create($user);
  }

  /**
   * Finds the record on the basis of given field and value
   * @param  String $field attrubute of Object
   * @param  String $value 
   * @return Object 
   */
  public function findBy($field, $value)
  {
    return $this->user->findBy($field, $value)->first();
  }

  /**
   * Updates the record
   * @param  Object $user
   * @param  Array $data 
   * @return Integer
   */
  public function update($user, $data)
  {
     return $this->user->update($user, $data);
  }

  /**
   * Updates Device Model
   * @param  Object $device 
   * @param  Array $data
   * @return Integer    
   */
  public function updateDeviceInfo($device, $data)
  {
    $this->user->setDeviceModel();
    return $this->user->update($device, $data);
  }
}