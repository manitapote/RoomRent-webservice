<?php 

namespace Roomrent\Posts\Services;

use Roomrent\Helpers\ResponseHelper;
use Roomrent\Helpers\ImageHelper;
use Roomrent\Posts\Repositories\PostRepositoryInterface;

class PostService
{
    /**
     * Object to bind PostRepositoryInterface
     * @var object Post
     */
    protected $post;

    /**
     * Object to bind ResponseHelper class
     * @var Roomrent\Helpers\ResponseHelper
     */
    protected $responseHelper;

    /**
     * Object to bind ImageHelper class
     * @var Roomrent\Helpers\ImageHelper
     */
    protected $imageHelper;

    /**
     * Constructor
     * @param PostRepositoryInterface $post           
     * @param ResponseHelper          $responseHelper 
     * @param ImageHelper             $imageHelper    
     */
    public function __construct(
    	PostRepositoryInterface $post, 
    	ResponseHelper $responseHelper, 
    	ImageHelper $imageHelper
    	)
    {
    	$this->post           = $post;
    	$this->responseHelper = $responseHelper;
    	$this->imageHelper 	  = $imageHelper;
    }

     /**
     * Adds image to the post object
     * 
     * @param  Array  $posts   array of objects
     * @return Array        
     */
    public function includeImageInPostResponse($posts)
    {
        collect($posts)->map(function($item) {
            $item['images'] = $item->images();
        });
    }

    /**
     * Adds user in the offer posts
     * @param  Array $posts Array of post objects
     * @return Array
     */
    public function includeUserInPostResponse($posts)
    {
    	collect($posts)->map(function($item) {
            $item->user;
        });
    }

    /**
     * Formats the response for post
     * @param  String  $request 
     * @param  String  $code    
     * @param  Array   $posts   Array of post object
     * @param  Integer $total   
     * @param  Integer $count   
     * @return Array          
     */
    public function formatPostResponse($request, $code, $posts, $total, $count)
    {
        $this->includeImageInPostResponse($posts);

        $offset       = $request->offset? $request->offset : 0;
        $currentCount = $offset + $count;
    	$lastPage     = ($total == $currentCount)? true : false ;

    	if (!$request->user)
    		$this->includeUserInPostResponse($posts);

        $response = $this->responseHelper->jsonResponse([
           'code'     => $code,
           'posts'    => $posts,
           'count'    => $count,
           'offset'   => ($currentCount < $total) ? $currentCount : 0,
           'total'    => $total,
           'lastPage' => $lastPage,
           ],
           $total);

        return $response;
    }

    /**
     * Gets the post by filtering according to request
     * @param  String $request 
     * @return Array           Array of Post object
     */
    public function filterPost($request)
    {
        if ($request->user == "true") {
        	$postsQuery = $this->post->getById(auth()->user()->user_id);

        	return ($request->offer_or_ask)
        		? $this->post->appendQueryField(
               $postsQuery, 'offer_or_ask', $request->offer_or_ask)
        		: $postsQuery;
        }

        if ($request->offer_or_ask) {
            return $this->post->getByField('offer_or_ask', $request->offer_or_ask);
        }

        return $this->post->getAll();

    }

    /**
     * Gets the post by skipping the offset posts
     *     
     * @param  String  $query  
     * @param  Integer $offset 
     * @return Array           Array of post object
     */
    public function getSkipPosts($query, $offset = 0)
    {
    	return $query->skip($offset)->take(config('constants.POST_SIZE'))->get();
    }

    /**
     * Gets Post related data from request
     * @param  String $request 
     * @return Array          
     */
    public function getPostDataFromRequest($request)
    {
    	$data            = $request->all();
        $data['user_id'] = auth()->user()->user_id;
       
        return $data;
    }

    /**
     * Saves images posted in offer
     * @param  String  $request 
     * @param  Integer $postId  
     * @return Array           Array of image names
     */
    public function savePostImage($request, $postId)
    {
    	$images = [];
    	if ($files = $request->file('file')) {
            foreach($files as $file) {
                $filename = $this->imageHelper->saveImage(
                    $file, config('constants.POST_IMAGE_FOLDER'));

                $image = $this->post->createImage([
                    'post_id' => $postId,
                    'imageName' => $filename,
                ]);

                array_push($images, $image->imageName);
            }
            
            return $images;
        }
    }
}