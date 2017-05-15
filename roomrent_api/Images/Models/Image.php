<?php

namespace Roomrent\Images\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	/**
	 * Fillable entities
	 * @var Array
	 */
	protected $fillable = ['post_id', 'imageName'];

	/**
	 * Hidden entities
	 * @var Array
	 */
	protected $hidden = ['id','updated_at', 'created_at'];
}
