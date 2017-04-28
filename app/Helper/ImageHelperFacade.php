<?php

namespace App\Helper;

use Illuminate\Support\Facades\Facade;
use App\Helper\ImageHelper;

class ImageHelperFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'imagehelper';
	}
}