<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	protected $fillable = ['post_id', 'imageName'];

	protected $hidden = ['id','updated_at', 'created_at'];
}
