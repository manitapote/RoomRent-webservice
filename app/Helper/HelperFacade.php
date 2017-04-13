<?php

namespace App\Helper;

use Illuminate\Support\Facades\Facade;
use App\Helper\Helper;

class HelperFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'helper';
	}
}