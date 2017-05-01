<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use ImageHelper;
use App\Helper;

class ImageController extends Controller
{
	protected $helper;
	   public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

	/**
	 * Gets the image form the storage
	 * @param  String $filename name of the image file
	 * @return Array            Image and mime type
	 */
	public function getImage($filename)
	{
		$image = ImageHelper::getImage($filename);
		
		if (!$image)
			return $this->helper->userResponse(['code' => '0062']);
		return response($image['image'])
			->header('Content-Type', $image['mimeType']);
	}
}