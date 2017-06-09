<?php

namespace Roomrent\Posts\Controller;

use Roomrent\ApiController;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Roomrent\Posts\Requests\PostRequest;
use Roomrent\Posts\Services\PostService;
use Roomrent\Helpers\ResponseHelper;
use Roomrent\Traits\HelperTrait;

class PostController extends ApiController
{
    use HelperTrait;

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
        $column    = ($request->details == "false")?
            ['title','longitude', 'latitude', 'offer_or_ask', 'id'] : ['*'];
            
        $posts = $this->postService->getSkipPosts(
                $postQuery, $request, $column);
      
        return response($this->postService->formatPostResponse(
            $request, '0072', $posts, $total, $posts->count()
        ));
    }


    /**
     * Gets the post of particuler Id
     * 
     * @param  Integer $id
     * @return JSON
     */
    public function getParticulerPost($id)
    {
        $post = $this->postService->findBy('id', $id, 'post')->first();

        if (!$post) {
            return response($this->responseHelper->jsonResponse(['code' => '0081', 'post' => $post]));
        }

        $data[0] = $post;

        $this->postService->includeImageInPostResponse($data);
        $this->postService->includeUserInPostResponse($data);

        return $this->responseHelper->jsonResponse(['code' => '0072', 'posts' => $post], 1);
    }

    /**
     * Store new post in database
     * 
     * @param PostRequest $request 
     * @return json of the Post data
     */
    public function setPost(PostRequest $request)
    {
        $data     = $this->postService->getPostDataFromRequest($request);
        $post     = $this->postService->create($data);

        if (!$post) {
            return $this->responseHelper->jsonResponse([
                'code' => '0000']);
        }

        $postType = $request->offer_or_ask == config('constants.OFFER')
        	? 'Offer' : 'Ask';

        $post['images'] = $this->postService->savePostImage($request, $post);

        return $this->postService->syncNotification($post);
        return $this->postService->fireNotification($post);
        

        return $this->responseHelper->jsonResponse([
            'code'      => '0073',
            'post'      => $post,
            'post_type' => $postType],
            $postType);
    }

    /**
     * Updates the selected post
     * 
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
            $this->postService->savePostImage($request, $post);

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
     * 
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
        $data['price']            = 8000;
        $data['no_of_rooms']      = 2;
        $data['offer_or_ask']     = 2;
        $data['latitude']         = 27.6894055;
        $data['longitude']        = 85.3226683;
        $data['id']               = 2;
        $data['title']            = "Room in patan";
        $data['post_description'] = "2 rooms well furnished available in patan";
        $data['location']         = "Thapathali";
        $data["created_at"]       = "2017-05-29 05:14:46";
        $data['updated_at']       = "2017-05-29 05:14:46";
        $data['images'] = [
            "http://192.168.0.136:81/api/image/1496034888phpbHWTQv.png",
            "http://192.168.0.136:81/api/image/1496034888php3WDC8X.png"
          ];
        $data['user'] = [
            "id" => 5,
            "name" => "Pote Manita",
            "username" => "manita pote",
            "email" => "mantiapote@ebpearls.com",
            "phone" => "123545909",
            "profileImage" => "http://192.168.0.136:81/api/image/1496034591phpeuG9bf.png"
          ];

        return $this->postService->fireNotification($data);
        $decoded = json_decode($result);

       return response(["success" => $decoded->success]);
    }


    /**
     * Deletes the selected posts
     * @param  Request $request 
     * @return JSON
     */
    public function deletePosts(Request $request)
    {
        $postIdArray = $this->postService->getPostIdsOfUser();        
        $commonId    = array_intersect($request->id, $postIdArray);
        
        if (!$commonId)
            return $this->responseHelper->jsonResponse(['code' => '0071']);

        if (!$count = $this->postService->deletePosts($commonId))
            return $this->responseHelper->jsonResponse(['code' => '0000']);

        return $this->responseHelper->jsonResponse(['code' => '0001'], "deleted ".$count." records");
    }

    public function getPostsForSync(Request $request)
    {
        $posts = $this->postService->filterPostForSync($request);
        return $posts;
        $this->postService->syncNotification($posts);
        
        return response($posts);
    }


}
