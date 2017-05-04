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
	
	/**
     * @SWG\Get(
     *     path="/image/{filename}",
     *     tags={"post"},
     *     summary="gets Image",
     *     description="Image",
     *     operationId="getImage",
     *     produces={"image/*"},
     *     security={
     *             {"api_key":{}}
     *      },
     *      @SWG\Parameter(
     *         in="path",
     *         name="filename",
     *         description="name of image file",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(response="405", description="Invalid inputs")
     * )
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