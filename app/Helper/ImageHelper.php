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
    public function includeImageUserInPostResponse($posts)
    {
        collect($posts)->map(function($item) {
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
        $filename = sprintf("%s%s.%s",
            time(),
            $file->getFilename(),
            $file->getClientOriginalExtension()
        );

        $file->storeAs($folder, $filename);
        
        return $filename;
    }

    /**
     * Gets the image from the folder
     * 
     * @param  String $filename Name of the image
     * @return Array            Image file and mimetype of file
     */
    public function getImage($filename)
    {
        $folder = Storage::disk('local')->exists(
                "/".config('constants.PROFILE_IMAGE_FOLDER')."/".$filename)
                ? config('constants.PROFILE_IMAGE_FOLDER'):
                (Storage::disk('local')->exists(
                "/".config('constants.POST_IMAGE_FOLDER')."/".$filename) 
                ? config('constants.POST_IMAGE_FOLDER') : null);

        if($folder == null)
        {
            return false;
        }

        $data['image'] = Storage::get("/$folder/".$filename);
        $data['mimeType']   = Storage::mimeType("/$folder/".$filename);

        return $data;
    }
}
