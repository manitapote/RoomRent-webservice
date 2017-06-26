<?php

namespace Roomrent\Images\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
	use SoftDeletes;
	
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
