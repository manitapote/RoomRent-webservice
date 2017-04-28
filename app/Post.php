<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Image;

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
		return Image::wherePostId($this->id)->pluck('image_location');
	}
}
