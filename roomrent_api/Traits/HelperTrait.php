<?php

namespace Roomrent\Traits;

trait HelperTrait {
	
	public function addURLInImage($images)
    {
        if ($images) {
            $imageURL = collect($images)->map(function($item) {
                return url('/api/image')."/".$item;
            });
            return $imageURL;
        }

        return $images;
    }

}