<?php 

namespace Roomrent\Posts\Services;

use Roomrent\Helpers\ResponseHelper;
use Roomrent\Helpers\ImageHelper;
use Roomrent\Posts\Repositories\PostRepositoryInterface;
use Roomrent\Helpers\PostHelper;
use Illuminate\Support\Collection;
use Roomrent\Traits\HelperTrait;
use App\Events\PostCreated;

class PostService
{
    use HelperTrait;
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
     * Object to bind PostHelper
     * @var PostHelper
     */
    public $postHelper ;

    /**
     * Constructor
     * @param PostRepositoryInterface $post           
     * @param ResponseHelper          $responseHelper 
     * @param ImageHelper             $imageHelper    
     * @param PostHelper              $postHelper
     */
    public function __construct(
    	PostRepositoryInterface $post, 
    	ResponseHelper $responseHelper, 
    	ImageHelper $imageHelper,
        PostHelper  $postHelper
    	)
    {
    	$this->post           = $post;
    	$this->responseHelper = $responseHelper;
    	$this->imageHelper 	  = $imageHelper;
        $this->postHelper     = $postHelper;
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
            $images         = $item->images()->pluck('imageName');
            $item['images'] = $this->addURLInImage($images);

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
            if ($item['user']['profileImage'])
                $item['user']['profileImage'] = $this->addURLInImage($item['user']['profileImage']);
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
        $offset       = $request->offset? $request->offset : 0;
        $currentCount = $offset + $count;
        $lastPage     = ($total == $currentCount)? true : false ;

        if (!$request->user == 'true' && !$request->details == 'false') {
            $this->includeUserInPostResponse($posts);
        }

        if ($request->user == "false" && $request->details == 'true') {

            $this->includeUserInPostResponse($posts);
        }

        if (!($request->details == "false")) {
            $this->includeImageInPostResponse($posts);
        }

        if ($request->details == 'false')
            return $this->responseHelper->jsonResponse([
                'code' => $code,
                'posts' =>$posts,
                ], $total);

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
            $postsQuery = $this->post->findBy('user_id',auth()->user()->user_id);

            return ($request->offer_or_ask)
                ? $this->post->appendQueryField(
               $postsQuery, 'offer_or_ask', $request->offer_or_ask)
                : $postsQuery;
        }

        if ($request->offer_or_ask) {
            return $this->post->findBy('offer_or_ask', $request->offer_or_ask);
        }

        return $this->post->getAll();

    }

    /**
     * Gets the latest updated and inserted posts
     *     
     * @param  Request $request
     * @return Array            array of posts if found else null
     */
    public function getUpdatedAndInsertedPosts($request)
    {
        $posts = $this->post->findBy('updated_at', $request->timestamp, '>')->get();

        if ($posts) {
            $this->includeUserAndImageInPosts($posts);
    
            $data['insertedPosts'] = array();
            $data['updatedPosts']  = array();
            
            foreach($posts as $post)
            {
                if ($post->created_at == $post->updated_at) {
                    array_push($data['insertedPosts'], $post);
                    continue;
                }  

                array_push($data['updatedPosts'], $post);
            }
            return $data;
        }

        return null;
    }

    /**
     * Gets deleted posts
     *         
     * @param  Request $request 
     * @return Array            array of posts if found else null
     */
    public function getDeletedPosts($request)
    {
        $deletedPostsQuery = $this->post->onlyTrashed();
        $deletedPosts      = $this->post->appendQueryField(
            $deletedPostsQuery, 'deleted_at', $request->timestamp, '>')->get();
        
        if ($deletedPosts->isEmpty()) {
            return null;
        }

        $this->includeUserAndImageInPosts($deletedPosts);

        return $deletedPosts;
    }

    /**
     * Includes User and Image information in post response
     * @param  Post $posts 
     * @return 
     */
    public function includeUserAndImageInPosts($posts)
    {
        $this->includeImageInPostResponse($posts);
        $this->includeUserInPostResponse($posts);
    }

    /**
     * Gets the post by skipping the offset posts
     *     
     * @param  String  $query  
     * @param  Integer $offset 
     * @return Array           Array of post object
     */
    public function getSkipPosts($query, $request, $column = ['*'])
    {
        if ($request->details == "false") 
            return $query->get($column);

    	return $query->skip($request->offset)->take(config('constants.POST_SIZE'))->get($column);
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
    public function savePostImage($request, $post)
    {
    	if ($files = $request->file('file')) {
            foreach($files as $file) {
                $filename = $this->imageHelper->saveImage(
                    $file, config('constants.POST_IMAGE_FOLDER'));

                $this->post->setImageModel();
                $image = $this->post->create([
                    'post_id' => $post->id,
                    'imageName' => $filename,
                ]);
            }
            
        }

        $images = $post->images()->pluck('imageName');

        return $this->addURLInImage($images);
    }

    /**
     * Creates new Post
     * @param  Array $data 
     * @return Object
     */
    public function create($data)
    {
        return $this->post->create($data);
    }

    /**
     * Gets the Near By location around given latitude and longitude
     * @param  Array $request 
     * @return Query
     */
    public function getByLocation($request)
    {
        $distance = isset($request['distance'])? $request['distance'] : config('constants.DISTANCE');
        $data  = $this->postHelper->calculateLatLongRange(
            $distance, $request['latitude'], $request['longitude']);

        $field     = 'latitude';
        $postQuery = $this->post->getBetween($field, $this->formatArray($field,  $data));
        $field     = 'longitude';

        return $this->post->appendWhereBetweenQuery(
            $postQuery,$field, $this->formatArray($field, $data));
    }

    /**
     * Ginds the record according to given value form given model
     * @param  String $field
     * @param  String $value
     * @param  String $model Model name
     * @return Object
     */
    public function findBy($field, $value, $model)
    {
        if ($model == 'image') {
            $this->post->setImageModel();
        }

        return $this->post->findBy($field, $value);
    }

    /**
     * Checks if the particuler post belongs to the particuler user
     * @param  Integer $id 
     * @return Object
     */
    public function checkPostBelongToUser($id)
    {
        $postQuery = $this->findBy('id', $id, 'post');
        $post      = $this->post->appendQueryField(
            $postQuery, 'user_id', auth()->user()->user_id)->first();
        if ($post) {
            return $post;
        }

        return null;
    }

    /**
     * Fires push notifications to the users
     * @param  Array $data criterias
     * @return Array       
     */
    public function fireNotification($data)
    {
       $posts = $this->matchingPosts($data);
       if ($posts) {
           $userIdArray      = collect($posts)->pluck('user_id');
           $userIdArray = $userIdArray->toArray();

            foreach (
            array_keys($userIdArray, auth()->user()->user_id) as $key) {
                unset($userIdArray[$key]);
            }

           $deviceTokenArray = $this->getDataFromDeviceModel('user_id', $userIdArray, 'device_token');

           $this->includeUserInPostResponse(['0' => $data]);
           
           return event(new PostCreated($deviceTokenArray, $data)); 
           
        }
    }

    /**
     * Gets devicet_token for sync notification
     * @param  Array $data 
     * @return fires event       
     **/
    public function syncNotification($data)
    {
        $this->post->setDeviceModel();

        $deviceTokenArray = $this->post->findBy(
            'api_token', null, '!=')->pluck('device_token');

        event(new PostCreated($deviceTokenArray, $data));
    }

    /**
     * Gets the particuler field data from the device model
     * @param  String $field      
     * @param  Array  $fieldArray Array of field values to satisfy
     * @param  String $pluckField field value to be collected
     * @return Array              Array of collected values
     */
    public function getDataFromDeviceModel($field, $fieldArray, $pluckField)
    {
        $this->post->setDeviceModel();
        $query =  $this->post->whereIn($field, $fieldArray);

        return $this->post->whereNotNull($query, 'api_token')->pluck($pluckField);

    }

    /**
     * Filters posts for push notifications
     * @param  Array $data Credentials
     * @return Query
     */
    public function matchingPosts($data)
    {
        $requiredPostType = $data['offer_or_ask'] == config('constants.OFFER') ?
            config('constants.ASK') : config('constants.OFFER') ;

        $this->post->setPostModel();

        $locationQuery    = $this->getByLocation($data); 
        $priceQuery       = $this->post->appendWhereBetweenQuery(
        $locationQuery, 'price', ['price_min' => 0, 'price_max' => $data['price'] + 2000]);
        $posts            = $this->post->appendQueryField(
        $priceQuery, 'offer_or_ask', $requiredPostType)->get();
        if ($posts) {
            $this->includeImageInPostResponse($posts);
            $this->includeUserInPostResponse($posts);

        return $posts;
       }
    }
    
    /**
     * Updates the given record
     * @param  Object $model 
     * @param  Array $data
     * @return Integer
     */
    public function update($model, $data)
    {
        return $this->post->update($model, $data);
    }

    /**
     * Formats array for whereBetween query
     * @param  String $field
     * @param  Array $data
     * @return Array            Key, value pair
     */
    public function formatArray($field, $data)
    {
        return [$field."_min" => $data[$field."_min"],
            $field."_max" => $data[$field."_max"]];
    }

    /**
     * Plucks only user if from the array of posts
     * 
     * @return Array Array of user id
     */
    public function getPostIdsOfUser()
    {
        $userId = auth()->user()->user_id;
        $postIdArray = $this->post->findBy('user_id', $userId)->pluck('id')->toArray();
       return $postIdArray;
    }

    /**
     * Deletes the post with given id
     * @param  Integer $id 
     * @return Integer     no of rows deleted
     */
    public function deletePosts($id)
    {
        return $this->post->destroy($id);
    }

    public function filterPostForSync($request)
    {
        $responsePost = [];

        if (!$request->timestamp) {
            $posts = $this->post->getAll()->get();

            if ($posts) {
                $this->includeUserAndImageInPosts($posts);
                $count = $posts->count();

                return response($this->responseHelper->jsonResponse(
                    ['code' => '0072', 'posts' => $posts], $count));
            }

            return response($this->responseHelper->jsonResponse(
                ['code' => '0071']));
        }

        $data         = $this->getUpdatedAndInsertedPosts($request);
        $data['deletedPosts'] = $this->getDeletedPosts($request);

        if (($data['insertedPosts'] == null) && ($data['updatedPosts'] == null) && ($data['deletedPosts'] == null)) {
            return $this->responseHelper->jsonResponse(['code' => '0071']);
        }

        if ($data['insertedPosts']) {
            $responsePost['created'] = [
                'posts' => $data['insertedPosts'],
                'count' => count($data['insertedPosts']),

            ];
        }

        if ($data['updatedPosts']) {
            $responsePost['updated'] = [
                'posts' => $data['updatedPosts'],
                'count' => count($data['updatedPosts']),
            ];
        }

        if ($data['deletedPosts']) {
            $responsePost['deleted'] = [
                'posts' => $data['deletedPosts'],
                'count' => count($data['deletedPosts']),
                ];
        }

        $responsePost['code'] = '0001';
        
        return response($this->responseHelper->jsonResponse($responsePost, 'found records'));
    }

}