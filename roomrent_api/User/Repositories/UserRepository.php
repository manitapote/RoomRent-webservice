<?php

namespace Roomrent\User\Repositories;

use Roomrent\User\Models\User;
use Roomrent\User\Models\Device;
use Roomrent\User\Repositories\UserRepositoryInterface;
use Roomrent\User\Repositories\DeviceRepositoryInterface;


class UserRepository implements UserRepositoryInterface
{
  /**
   * Object to bind to User Model 
   * @var Object
   */
	protected $user;

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
		$this->user = $user;
		$this->device = $device;
	}

	 
  /**
   * Creates the user
   * 
   * @param  User $user 
   * @return User
   */
  public function createUser($user)
  {
     $user = $this->user->create($user);
     
     return $user;  
  }

  /**
    * Updates the user in database
    * 
    * @param  User   $user 
    * @param  Array $data  
    * @return User       
    */
   public function updateUser($user, $data)
   {
      return ($user->update($data));
   }

    /**
   * Gets user by the given field
   * 
   * @param  String $field Database field
   * @param  String $value 
   * @return User
   */
  public function getUserByField($field, $value)
  {
    return ($this->user->where($field, $value)->first());
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

   /**
    * Updates the field of device table
    * @param  Object $device 
    * @param  Array  $data   
    * @return Object
    */
   public function updateDeviceInfo($device, $data)
   {
   		return ($device->update($data));
   }
}