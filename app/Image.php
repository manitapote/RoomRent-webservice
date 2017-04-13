<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	protected $fillable = ['post_id', 'image_location'];

	protected $hidden = ['id','updated_at', 'created_at'];
}
