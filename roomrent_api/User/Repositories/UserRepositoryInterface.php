<?php

namespace Roomrent\User\Repositories;

/**
 * Interface for UserRepository
 */
interface UserRepositoryInterface
{
	public function storeDeviceInfo($request, $userId);
}