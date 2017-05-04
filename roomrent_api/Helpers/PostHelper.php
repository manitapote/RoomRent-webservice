<?php

namespace Roomrent\Helpers;

class PostHelper
{
	/**
    * calculate the max and min range of latitude and logitudewithin given 
    * distance
    * 
    * @param  Ingeger $distance  Distance from the given point
    * @param  Decimal $latitude  Latitude of the place
    * @param  Decimal $longitude Longitude of the place
    * @return Array              Array containing the range oflatitude
    *                            and longitude
    */
	public function calculateLatLongRange($distance, $latitude, $longitude)
	{
		$data             = [];
		$ratio            = $distance / (config('constants.RADIUS'));

		$latitude         = $this->convertDegreeToRadian($latitude);
		$longitude        = $this->convertDegreeToRadian($longitude);

		$data['latitude_max']  = $this->convertRadianToDegree($latitude + $ratio);
		$data['latitude_min']  = $this->convertRadianToDegree($latitude - $ratio);

		$delLong          = asin(sin($ratio)/cos($latitude));

		$data['longitude_max'] = $this->convertRadianToDegree($longitude + $delLong);
		$data['longitude_min'] = $this->convertRadianToDegree($longitude - $delLong);

		return $data; 

	}

    /**
     * Converts the angle in degree to radian
     * @param  Decimal $degree 
     * @return Decimal          Angel in radian
     */
    public  function convertDegreeToRadian($degree)
    {
    	return ($degree * pi() / 180);
    }

    /**
     * Converts angle in radian to degree
     * @param  Decimal $radian 
     * @return Decimal         
     */
    public function convertRadianToDegree($radian)
    {
    	return ($radian * 180/ pi());
    }

}