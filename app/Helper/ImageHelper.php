<?php 

namespace App\Helper;

use App\Image;
use App\User;

class ImageHelper 
{
	/**
	 * add image and user to the post object
	 * 
	 * @param  Array  $posts   array of objects
	 * @return Array        
	 */
	public function includeImageUserInPost($posts)
	{
		collect($posts)->map(function($item){
			     $item['images'] = $item->images();
			     $item->user;
			});
	}

	/**
	 * Saves image to given path
	 * 
	 * @param  File   $file Image file
	 * @param  String $path Path where to store the file
	 * @return String       File name
	 */
	public function saveImage($file, $path)
	{
		$filename = time().$file->getFilename().'.'.
					$file->getClientOriginalExtension();
		$file->move($path, $filename);
		
		return $filename;
	}
}
