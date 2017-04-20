<?php 

namespace App\Helper;

class Helper 
{
	/**
	 * makes array of given objects
	 * 
	 * @param  Array  $items   array of objects
	 * @param  string $field   field to retrieve from object
	 * @return Array        
	 */
	public function pushArray($items, $field)
	{
		$collection = [];
		foreach ($items as $key => $item){
			array_push($collection, $item->$field);	
		}
		return $collection;
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
