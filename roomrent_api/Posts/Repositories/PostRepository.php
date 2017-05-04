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
	 * Constructor
	 * @param Post  $post  
	 * @param Image $image 
	 */	
	public function __construct(Post $post, Image $image, Device $device)
	{
		$this->model = $post;
		$this->image = $image;
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
}