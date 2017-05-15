<?php

namespace Roomrent\Posts\Controller;

use Roomrent\ApiController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Roomrent\Posts\Requests\PostRequest;
use Roomrent\Posts\Services\PostService;
use Roomrent\Helpers\ResponseHelper;
use Roomrent\Helpers\PostHelper;

use Roomrent\Posts\Repositories\PostRepositoryInterface;

class PostController extends ApiController
{
    /**
     * Object to bind the responseHelper
     * @var ResponseHelper
     */
    protected $responseHelper;

    /**
     *Object to bind PostRepository
     * @var Post
     */
    protected $post;

    /**
     * Object to bind PostService
     * @var object User
     */
    protected $postService;

    /**
     * Object to bind PostHelper
     * @var PostHelper
     */
    public $postHelper ;

    /**
     * Constructor
     * @param PostService             $postService   
     * @param ResponseHelper          $responseHelper
     * @param PostRepositoryInterface $post          
     * @param PostHelper              $postHelper    
     */
    public function __construct(
        PostService $postService,
    	ResponseHelper $responseHelper, 
        PostRepositoryInterface $post,
        PostHelper $postHelper)
    {
        $this->postService    = $postService;
        $this->responseHelper = $responseHelper;
        $this->post           = $post;
        $this->postHelper     = $postHelper;
    }

    /**
     * Gets posts according to query strings
     * 
     * @param query strings offset, user, offer_or_ask
     * @return json object of multiple Post
     * 
     * @SWG\Get(
     *     path="/post",
     *     tags={"post"},
     *     summary="Post according to query string",
     *     description="gets post for loggedin user",
     *     operationId="getPost",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="query",
     *         name="offset",
     *         description="gets the data skipping no of offset posts",
     *         required=false,
     *         type="integer"
     *     ),
     *      @SWG\Parameter(
     *         in="query",
     *         name="user",
     *         description="gets the post of user",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         in="query",
     *         name="offer_or_ask",
     *         description="gets the offer or ask of user",
     *         required=false,
     *         type="integer"
     *     ),
     *     security={
     *             {"api_key":{}}
     *     },
     *     @SWG\Response(response="405", description="Invalid inputs")
     * )
     */
    public function getPost(Request $request)
    {
        $postQuery = $this->postService->filterPost($request);
        $total     = $postQuery->count();
        $posts     = $this->postService->getSkipPosts($postQuery, $request->offset);

        return response($this->postService->formatPostResponse(
            $request, '0072', $posts, $total, $posts->count()
        ));
    }

    /**
     * Store new post in database
     * 
     * @param PostRequest $request 
     * @return json of the Post data-+
     * 
     */
    
    /**
     * @SWG\Post(
     *     path="/post/create",
     *     tags={"post"},
     *     summary="creates post",
     *     description="creates post for loggedin user",
     *     operationId="setPost",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="formData",
     *         name="title",
     *         description="title of post",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="post_description",
     *         description="description of the post",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="location",
     *         description="Address",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="latitude",
     *         description="latitude of the location",
     *         required=true,
     *         type="number"
     *     ),
     *    @SWG\Parameter(
     *         in="formData",
     *         name="longitude",
     *         description="longitude of the place",
     *         required=true,
     *         type="number"
     *     ),
     *    @SWG\Parameter(
     *         in="formData",
     *         name="price",
     *         description="price of the post",
     *         required=true,
     *         type="integer"
     *     ),
     *    @SWG\Parameter(
     *         in="formData",
     *         name="no_of_rooms",
     *         description="number of rooms",
     *         required=true,
     *         type="integer"
     *     ),
     *    @SWG\Parameter(
     *         in="formData",
     *         name="offer_or_ask",
     *         description="1 for offer 2 for ask",
     *         required=true,
     *         type="integer"
     *     ),
     *    @SWG\Parameter(
     *         in="formData",
     *         name="file[0]",
     *         description="image file",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="file[1]",
     *         description="image file",
     *         required=false,
     *         type="file"
     *     ),
     *     security={
     *             {"api_key":{}}
     *      },
     *     @SWG\Response(response="405", description="Invalid inputs")
     * )
     */
    public function setPost(PostRequest $request)
     {
        $data     = $this->postService->getPostDataFromRequest($request);
        $post	  = $this->post->create($data);
        $postType = $request->offer_or_ask == config('constants.OFFER')
        	? 'Offer' : 'Ask';

        $post['images'] = $this->postService->savePostImage($request, $post->id);

        return $this->responseHelper->jsonResponse([
            'code' => '0073',
            'post' => $post,
            'post_type' => $postType],
            $postType);
    }

    /**
     * gets posts filtered by location around certain distance
     * 
     * @param  Request $request latitude and longitude
     * @return Post             Json object
     * 
     * @SWG\Post(
     *     path="/postbylocation",
     *     tags={"post"},
     *     summary="post near the given location",
     *     description="gets post around a certain distance",
     *     operationId="getPostByLocation",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="formData",
     *         name="latitude",
     *         description="latitude of a place",
     *         required=true,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="longitude",
     *         description="longitude of a place",
     *         required=true,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="offset",
     *         description="gets post after offset value",
     *         type="number"
     *     ),
     *     security={
     *             {"api_key":{}}
     *      },
     *     @SWG\Response(response="405", description="Invalid inputs")
     * )
     */
    public function getPostByLocation(Request $request)
    {
        $data  = $this->postHelper->calculateLatLongRange(
            config('constants.DISTANCE'),
            $request->latitude, $request->longitude);
    
        $postQuery = $this->post->getByLocation($data);
        $posts     = $this->postService->getSkipPosts($postQuery);
     
        return response(
            $this->postService->formatPostResponse(
                $request, '0072', $posts, $postQuery->count(), $posts->count()
            ));
    }
}
// 