<?php 

namespace App\Helper;

use Illuminate\Support\Facades\Storage;

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
	public function saveImage($file, $folder)
	{
		$filename = time().$file->getFilename().'.'.
					$file->getClientOriginalExtension();
		$file->storeAs($folder, $filename);
		
		return $filename;
	}

	public function getImage($filename)
	{
		$data                  = [];
		$folder                = Storage::disk('local')
				                 ->exists("/".config(
				                 'constants.PROFILE_IMAGE_FOLDER').
				                 "/".$filename) ? 
				                 config('constants.PROFILE_IMAGE_FOLDER') :
				                 config('constants.POST_IMAGE_FOLDER');
		if(!($data['image']    = Storage::get("/$folder/".$filename)))
				return false;
		$data['mimeType']      = Storage::mimeType("/".$folder."/".$filename);

		return $data;
	}
}
