<?php 

namespace Roomrent\Posts\Services;

use Roomrent\Helpers\ResponseHelper;
use Roomrent\Helpers\ImageHelper;
use Roomrent\Posts\Repositories\PostRepositoryInterface;
use Roomrent\Helpers\PostHelper;
use Illuminate\Support\Collection;
use Roomrent\Traits\ImageTrait;

class PostService
{
    use ImageTrait;
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

    // public function addURLInImage($images)
    // {
    //     if ($images) {
    //         $imageURL = collect($images)->map(function($item) {
    //             return url('/api/image')."/".$item;
    //         });
    //         return $imageURL;
    //     }

    //     return $images;
    // }

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
           $deviceTokenArray = $this->getDataFromDeviceModel('user_id', $userIdArray, 'device_token');
           $message          = /*$data['post_description'];*/'2 rooms in patan';
           $title            = /*$data['title']; */'room in patan';
          
          // $deviceTokenArray = ["dd9cl-vW_fY:APA91bH5eZ6kZJQnXl_w_2heLeu_xz3_YXh3prgrX3Iqmnjqo9r3afpTMOfzIOwXyKrQx_LK8ocebnI4MjJ2wRTnsr-HY85VpcVN_VwcfpzqJaIjW61L0ARWbhzw7O6nFrwe2ppLE-wQ"];

           return $this->pushnotification($deviceTokenArray, $message, $title, $data);
        }
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
        return $this->post->whereIn($field, $fieldArray)->pluck($pluckField);
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
     * Post request to FCM
     * @param  String $tokens
     * @param  String $message 
     * @param  String $title   
     * @param  array  $data    
     * @return JSON 
     */
    public function pushnotification($tokens, $message, $title, $data) 
    {
        $post = [];
        $key = env('FCM_SERVER_KEY');
        $fields = array(
            'registration_ids' => $tokens,
            // 'notification' => array(
            //     'body'=>$message,
            //     'title'=>$title,
            //     'sound'=>'default'),
            'priority' => 'high',
            'data' => $data);
        $headers = array(
            'Authorization: key='. $key,
            'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) { 
            return false;
        }
        curl_close($ch);
        return $result; 
    }

    public function getPostIdsOfUser()
    {
        $userId = auth()->user()->user_id;
        $postIdArray = $this->post->findBy('user_id', $userId)->pluck('id')->toArray();
       return $postIdArray;
    }

    public function deletePosts($id)
    {
        return $this->post->destroy($id);
    }
}