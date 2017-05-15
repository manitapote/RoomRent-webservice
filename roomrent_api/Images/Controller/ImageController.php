<?php

namespace Roomrent\Images\Controller;

use Roomrent\ApiController;
use Roomrent\Helpers\ImageHelper;
use Roomrent\Helpers\ResponseHelper;

class ImageController extends ApiController
{
    /**
     * Object to bind ImageHelper class
     * @var ImageHelper
     */
	protected $imageHelper;

    /**
     * Object to bind ResponseHelper
     * @var ResponseHelper
     */
    protected $responseHelper;

    /**
     * Constructer
     * @param ImageHelper    $imageHelper    
     * @param ResponseHelper $responseHelper 
     */
	public function __construct(
        ImageHelper $imageHelper, 
        ResponseHelper $responseHelper)
    {
        $this->imageHelper    = $imageHelper;
        $this->responseHelper = $responseHelper;
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
		$image = $this->imageHelper->getImage($filename);
		
		if (!$image)
			return $this->responseHelper->userResponse(['code' => '0062']);
		return response($image['image'])
			->header('Content-Type', $image['mimeType']);
	}
}