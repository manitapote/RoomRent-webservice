<?php

namespace Roomrent\Posts\Repositories;

use Roomrent\Posts\Models\Post;
use Roomrent\Images\Models\Image;
use Roomrent\User\Models\Device;
use Roomrent\Posts\Repositories\PostRepositoryInterface;
use Roomrent\Repository;

class PostRepository extends Repository implements PostRepositoryInterface
{
	/**
	 * Object to bind Post Model
	 * @var Object
	 */		
	protected $post;

	/**
	 * Object to bind Image Model
	 * @var Object
	 */
	protected $image;

	/**
	 * Object to bind Device Model
	 * @var  Object
	 */
	protected $device;

	/**
	 * Constructor
	 * @param Post    $post  
	 * @param Image   $image
	 * @param Device $device
	 */	
	public function __construct(Post $post, Image $image, Device $device)
	{
		$this->post   = $post;
		$this->model  = $post;
		$this->image  = $image;
		$this->device = $device;
	}


	public function setImageModel()
	{
		$this->model = $this->image;
	}

	public function setDeviceModel()
	{
		$this->model = $this->device;
	}

	public function setPostModel()
	{
		$this->model = $this->post;
	}
}