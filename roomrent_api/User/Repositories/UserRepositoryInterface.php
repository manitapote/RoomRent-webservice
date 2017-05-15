<?php

namespace Roomrent\User\Repositories;

/**
 * Interface for UserRepository
 */
interface UserRepositoryInterface
{
	public function createUser($user);

	public function updateUser($user , $data);

	public function getUserByField($field, $value);

	public function storeDeviceInfo($request, $userId);
	
	public function updateDeviceInfo($device, $data);
}