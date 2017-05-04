<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Image;

/**
 * @SWG\Definition(
 *	  required={"title", "post_description", "location",
 *	  	"latitude", "longitude", "price", "no_of_rooms", "offer_or_ask"},
 *	  type="object",
 *	  definition="Post",
 *	  @SWG\Property(property="title", type="string", example="Title"),
 *	  @SWG\Property(property="post_description", type="string",
 *	  	example="Room available at Patan"),
 *	  @SWG\Property(property="location", type="string", example="Patan"),
 *	  @SWG\Property(property="latitude", type="integer", example="000.00000000"),
 *	  @SWG\Property(property="longitude", type="integer", example="000.00000000"),
 *	  @SWG\Property(property="price", type="integer", example="3000"),
 *	  @SWG\Property(property="no_of_rooms", type="integer", example="2"),
 *	  @SWG\Property(property="offer_or_ask", type="integer", example="1")
 *)
 */
class Post extends Model
{
	/**
	 * mass assignable property
	 * @var array
	 */
	protected $fillable  = [
	'post_description','location','latitude','longitude','price',
	'no_of_rooms','title','user_id' , 'offer_or_ask'
	];

	/**
	 * properties hidden 
	 * 
	 * @var array
	 */
	protected $hidden = [
	 'deleted_at', 'user_id'
	];

	/**
	 * Gets the use belonged to the particular post
	 * 
	 * @return relationship returns user as property of the model
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Gets the image_location field of offer from image table
	 * 	
	 * @return relation returns offer_images as property of model
	 */
	public function images()
	{
		return Image::wherePostId($this->id)->pluck('imageName');
	}
}
