<?php

namespace Roomrent\User\Repositories;

use Roomrent\User\Models\User;
use Roomrent\User\Models\Device;
use Roomrent\User\Repositories\UserRepositoryInterface;
use Roomrent\Repository;


class UserRepository extends Repository implements UserRepositoryInterface
{
  /**
   * Object to bind to Device Model
   * @var Object
   */
	protected $device;

  /**
   * Constructor
   * @param User   $user   
   * @param Device $device 
   */
	public function __construct(User $user, Device $device)
	{
    $this->model = $user;
		$this->device = $device;
	}

  /**
   * Sets the model to Device model
   */
  public function setDeviceModel()
  {
   $this->model = $this->device;
  }
  
  /**
   * Store device info
   * 
   * @param  LoginRequest $request 
   * @param  Integer $userId 
   * @return Device          
   */
  public function storeDeviceInfo($request, $userId)
  {
      $deviceInfo = $this->device->updateOrCreate([
           'device_token' => $request->device_token, 
           'device_type'  => $request->device_type,
           'user_id'      => $userId,
      ],[
          'api_token'    => str_random(60)
      ]);

      return $deviceInfo;
  }
}