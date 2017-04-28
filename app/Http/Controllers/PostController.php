<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\PostRequest;
use App\Post;
use App\User;
use App\Image;
use App\Helper;

use Auth;
use ImageHelper;

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

	public $response = [];

	protected $offset;

	/**
	 * constant to denote offer or ask
	 */
	const OFFER    = 1;
	const ASK      = 2;
	const DISTANCE = 5;
	const SIZE     = 3;

	/**
	 * Constructer to bind object to mod el
	 * 
	 * @param Post $post 
	 * @param User $user
	 */
	public function __construct(Post $post, User $user, Image $image)
	{
		$this->post  = $post;
		$this->user  = $user;
		$this->image = $image;
	}

	/**
	 * Get all post related to particular user
	 * 
	 * @param string $userPost determines either to get offers or asks, possible
	 * value ('userOffer|userAsk')
	 * @return json object of multiple Post
	 */
	public function getUserPost(Request $request)
	{
		$offset = ($request->offset) ?  $request->offset : 0;
		$user   = Auth::guard('api')->user()->user;	
		$posts  = $user->posts;//->skip($offset)->take(self::SIZE)->get();

		ImageHelper::includeImageUserInPost($posts);
		$response            = Helper::postResponse('0072', $posts);
		$response['message'] = sprintf($response['message'],$posts->count());
		
		return response($response);
	}

	/**
	 * Gets all offers or asks if specified else return all post
	 * 
	 * @param  string $allPost
	 * @return json of multiple Post either all offer or all ask
	 */
	public function getAllPost(Request $request)
	{
		$offset = ($request->offset) ?  $request->offset : 0;
		$posts  = (!$request->offer_or_ask) ? 
				  $this->post :
				  $this->post->where('offer_or_ask',$request->offer_or_ask);
		$postAfterSkip  = $posts->skip($offset)->take(self::SIZE)->get();

		ImageHelper::includeImageUserInPost($postAfterSkip);
		
		$response = Helper::postResponse('0072', $postAfterSkip);
		$response = array_merge($response,[
					'message' => sprintf($response['message'],$posts->count()),
					'count'   => $postAfterSkip->count(),
					'offset'  => $offset,
					'total'   => $posts->count(), 
					]);

		return response($response);
	}

	/**
	 * Store new post in database
	 * 
	 * @param PostRequest $request 
	 * @return json of the Post data
	 */
	public function setPost(PostRequest $request)
	{
		$images             = [];
		$data	            = $request->all();
		$user 	            = Auth::guard('api')->user()->user;
		$data['user_id']    =  $user->id;
		$post               = $this->post->create($data);
		$postType			= ($request->offer_or_ask == 1)? 'Offer' : 'Ask' ;
		
		if($files = $request->file('file')){
			$path = base_path()."/resources/postImages";
			foreach($files as $file)
			{
				$filename = ImageHelper::saveImage($file, $path);
				$image    = $this->image->create([
							'post_id' => $post->id,
							'image_location' => $path.'/'.$filename,
							]);

				array_push($images, $image->image_location);
			}
			$post['images'] = $images;
		}

		$post['post_type']   = $postType;
		$post['user']        = $user;
		$response    	     = Helper::postResponse('0073',$post);
		$response['message'] = sprintf($response['message'],$postType);
	
		return $response;
	}

	/**
	 * gets posts filtered by location around certain distance
	 * 
	 * @param  Request $request latitude and longitude
	 * @return Post             Json object
	 */
	public function getLocationByDistance(Request $request)
	{
		$offset = ($request->offset) ? $request->$offset : 0;
		$data   = Helper::calculateLatLongRange( 
				  self::DISTANCE , $request->latitude, $request->longitude
				  );
		
		$posts  = $this->post->where([
				  ['latitude', '<=', $data['lat_max']],
				  ['latitude' , '>=', $data['lat_min']]
				  ])->where([
				  ['longitude', '<=', $data['long_max']],
				  ['longitude', '>=', $data['long_min']],
				  ])->skip($offset)->take(self::SIZE)->get();

		ImageHelper::includeImageUserInPost($posts);

		return $posts;
	}
}

