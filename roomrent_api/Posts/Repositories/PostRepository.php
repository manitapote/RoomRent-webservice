<?php

namespace Roomrent\Posts\Repositories;

use Roomrent\Posts\Models\Post;
use Roomrent\Images\Models\Image;
use Roomrent\Posts\Repositories\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
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
	public function __construct(Post $post, Image $image)
	{
		$this->post = $post;
		$this->image = $image;
	}

	/**
	 * Creates new post
	 * @param  Object $post Post Model
	 * @return Object       Post Model
	 */
	public function create($post)
	{
        return $this->post->create($post);
	}

	/**
	 * Gets post by Id
	 * @param  Integer $id 
	 * @return Object       Post 
	 */
	public function getById($id)
	{
			return $this->post->whereUserId($id);
	}

	/**
	 * Gets post by particuler field
	 * @param  String $field 
	 * @param  Array  $data  
	 * @return Array        Post
	 */	
	public function getByField($field, $data)
	{
		return $this->post->where($field, $data);
	}

	/**
	 * Creates new image data
	 * @param  Array  $data 
	 * @return Object        Image Model
	 */
	public function createImage($data)
	{
		return $this->image->create($data);
	}

	/**
	 * Gets posts by location
	 * @param  Array $data 
	 * @return Array        Post
	 */
	public function getByLocation($data)
	{
		return $this->post
            ->whereBetween('latitude', [$data['lat_min'], $data['lat_max']])
            ->whereBetween('longitude', [$data['long_min'], $data['long_max']]);
            
	}

	/**
	 * Appends the query to given query
	 * @param  Query  $query 
	 * @param  String $field field of post table
	 * @param  Array  $data  
	 * @return Query  
	 */
	public function appendQueryField($query, $field, $data)
	{
		return $query->where($field, $data);
	}

	/**
	 * Gets all posts
	 * @return Array array of post object
	 */
	public function getAll()
	{
		return $this->post;
	}
}