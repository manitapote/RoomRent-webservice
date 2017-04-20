<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Post;
use App\User;
use App\Image;
use Auth;
use Helper;

class PostController extends Controller
{
	/**
	 * inject object of Post model
	 * @var object Post
	 */
	protected $post;

	/**
	 * inject object of User model
	 * @var object User
	 */
	protected $user;

	/**
	 * inject object of Image model
	 * @var object Image
	 */
	protected $image;

	/**
	 * constant to denote offer or ask
	 */
	const OFFER = 1;
	const ASK = 2;

	/**
	 * Constructer to bind object to model
	 * 
	 * @param Post $post 
	 * @param User $user
	 */
	public function __construct(Post $post, User $user, Image $image)
	{
		$this->post = $post;
		$this->user = $user;
		$this->image = $image;
	}

	/**
	 * Get all post related to particular user
	 * 
	 * @param string $userPost determines either to get offers or asks, possible
	 * value ('userOffer|userAsk')
	 * @return json object of multiple Post
	 */
	public function getUserPost()
	{
		$user  = Auth::guard('api')->user()->user;	
		$posts = $user->userPost;
			$image  = Helper::pushArray($user->images, 'image_location');
			return $image;
		foreach ($posts as $key => $post) {
			if($post->offer_or_ask == '1'){
				$image  = Helper::pushArray($user->images, 'image_location');
				$post['image'] = $image;
				
			}
		}
		
		return response(['username' => $user->username, 'posts'=> $posts]);
	}

	/**
	 * Gets all offers or asks if specified else return all post
	 * 
	 * @param  string $allPost
	 * @return json of multiple Post either all offer or all ask
	 */
	public function getAllPost(Request $request)
	{
		if(!$request->offer_or_ask){
			$posts = $this->post->all();
			foreach ($posts as $post) {
				$post->user;
			}

			return $posts;
		}

		$choice = ($request->offer_or_ask == self::OFFER) ? 
				  self::OFFER : self::ASK;
		$posts = $this->post->where('offer_or_ask',$choice)->get();
		
		foreach ($posts as $post) {
			$post->user;
			if($choice == self::OFFER){
				$images = $this->image->wherePostId($post->id)->get();
				$imageCollection = Helper::pushArray($images, 'image_location');
				$post['images'] = $imageCollection;
			}

		}
		
		return response($posts);
	}

	/**
	 * Store new post in database
	 * 
	 * @param PostRequest $request 
	 * @return json of the Post data
	 */
	public function setPost(PostRequest $request)
	{
		$images  = [];
		$data	 = $request->all();
		$user 	 = Auth::guard('api')->user();
		$data    = array_merge($data,[
			'user_id' => $user->user_id,
			]);
		$post    = $this->post->create($data);
		
		if($files = $request->file('file')){
			$path = base_path()."/resources/postImages";
			foreach($files as $file)
			{
				$filename = Helper::saveImage($file, $path);
				$image = $this->image->create([
					'post_id' => $post->id,
					'image_location' => $path.'/'.$filename,
					]);

				array_push($images, $image->image_location);
			}
			$post['images'] = $images;
		}
		return response($post);
	}
}

