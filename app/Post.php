<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
	 * Gets the image_location field of offer from image table
	 * 	
	 * @return relation returns offer_images as property of model
	 */
	public function offerImages()
	{
		return $this->hasMany('App\Image')->select('image_location');
	}

	/**
	 * Gets the use belonged to the particular post
	 * 
	 * @return relationship returns user as property of the model
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
