<?php

namespace Roomrent\Posts\Controller;

use Roomrent\ApiController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Roomrent\Posts\Requests\PostRequest;
use Roomrent\Posts\Services\PostService;
use Roomrent\Helpers\ResponseHelper;
use Roomrent\Posts\Repositories\PostRepositoryInterface;

class PostController extends ApiController
{
    /**
     * Object to bind the responseHelper
     * @var ResponseHelper
     */
    protected $responseHelper;

    /**
     * Object to bind PostService
     * @var object User
     */
    protected $postService;

    /**
     * Constructor
     * @param PostService             $postService   
     * @param ResponseHelper          $responseHelper
     */
    public function __construct(
        PostService $postService,
    	ResponseHelper $responseHelper)
    {
        $this->postService    = $postService;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Gets posts according to query strings
     * 
     * @param query strings offset, user, offer_or_ask
     * @return json object of multiple Post
     * 
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

    public function getParticulerPost($id)
    {
        $post = $this->postService->findBy('id', $id, 'post')->first();

        if (!$post) {
            return $this->responseHelper->jsonResponse(['code' => '0081', 'post' => $post]);
        }

        $data[0] = $post;

        $this->postService->includeImageInPostResponse($data);

        return $this->responseHelper->jsonResponse(['code' => '0091', 'post' => $post]);
    }

    /**
     * Store new post in database
     * 
     * @param PostRequest $request 
     * @return json of the Post data-+
     * 
     */
    public function setPost(PostRequest $request)
    {
        $data     = $this->postService->getPostDataFromRequest($request);
        $post	  = $this->postService->create($data);

        if (!$post) {
            return $this->responseHelper->jsonResponse([
                'code' => '0000']);
        }

        $this->postService->fireNotification($data);

        $postType = $request->offer_or_ask == config('constants.OFFER')
        	? 'Offer' : 'Ask';

        $post['images'] = $this->postService->savePostImage($request, $post);

        return $this->responseHelper->jsonResponse([
            'code'      => '0073',
            'post'      => $post,
            'post_type' => $postType],
            $postType);
    }

    /**
     * Updates the selected post
     * @param  PostRequest $request 
     * @param  Integer     $id      
     * @return JSON
     */
    public function updatePost(PostRequest $request, $id)
    {
        $data      = $this->postService->getPostDataFromRequest($request);
       
        if ($post = $this->postService->checkPostBelongToUser($id)) {
            $this->postService->update($post, $data);
            // $images = $this->postService->findBy('post_id', $post->id, 'image')->get();
            $this->postService->savePostImage($request, $post->id);

            return response($this->responseHelper->jsonResponse([
                'code' => '0001'],
                'updated'));
        }

        return response($this->responseHelper->jsonResponse([
            'code' => '0071']));
    }

    /**
     * gets posts filtered by location around certain distance
     * 
     * @param  Request $request latitude and longitude
     * @return Post             Json object
     * 
     */
    public function getPostByLocation(Request $request)
    {
        $postQuery = $this->postService->getByLocation($request);
        $posts     = $this->postService->getSkipPosts($postQuery);
     
        return response(
            $this->postService->formatPostResponse(
                $request, '0072', $posts, $postQuery->count(), $posts->count()
            ));
    }

    /**
     * Gets post with matching criteria
     * @param  Request $request 
     * @return JSON
     */
    public function criteriaMatchingPosts(Request $request)
    {
        $post = $this->postService->matchingPosts($request);

        return response(['post' => $post]);
    }

    public function fire()
    {
        $data['price']        = 3000;
        $data['no_of_rooms']  = 2;
        $data['offer_or_ask'] = 1;
        $data['latitude']     = 0.0;
        $data['longitude']    = 0.0;

        $result  = $this->postService->fireNotification($data);
        $decoded = json_decode($result);

       return response(["success" => $decoded->success]);
    }

}