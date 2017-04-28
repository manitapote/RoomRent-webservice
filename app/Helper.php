<?php

namespace App;

class Helper
{
     /**
     * Array to store response code, message, errors or posts
     * @var array
     */
    protected $response = [];

    /**
     * calculate the max and min range of latitude and logitude within given 
     * distance
     * 
     * @param  Ingeger $distance  Distance from the given point
     * @param  Decimal $latitude  Latitude of the place
     * @param  Decimal $longitude Longitude of the place
     * @return Array              Array containing the range of latitude
     *                            and longitude
     */
    public function calculateLatLongRange($distance, $latitude, $longitude)
    {
        $data             = [];
        $ratio            = $distance / (config('constants.RADIUS'));

        $latitude         = $this->convertDegreeToRadian($latitude);
        $longitude        = $this->convertDegreeToRadian($longitude);
        
        $data['lat_max']  = $this->convertRadianToDegree($latitude + $ratio);
        $data['lat_min']  = $this->convertRadianToDegree($latitude - $ratio);

        $delLong          = asin(sin($ratio)/cos($latitude));
        
        $data['long_max'] = $this->convertRadianToDegree($longitude + $delLong);
        $data['long_min'] = $this->convertRadianToDegree($longitude - $delLong);

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

    /**
     * @param  string $code status code 
     * @return string message related with status code
     */
    public function code($code) {
        $codes = array(
            // basic messages
            '0000'  =>  'Error occurred',
            '0001'  =>  'Success',
            // login messages
            '0011'  =>  'Successfully logged in',
            '0012'  =>  'Login failed. Incorrect %s and/or password',
            // registration messages
            '0013'  =>  'User registered',
            '0014'  =>  'Validation Errors',
            '0015'  =>  'Email verified',
            '0016'  =>  'Unable to send mail',
            // logout message
            '0020'  =>  'Logged out successfully',
            // profile update messages
            '0021'  =>  'Old password doesn\'t match',
            '0022'  =>  'Email address not found',
            '0023'  =>  'A password reset link has been sent to your nominated email address',
            '0024'  =>  'Password successfully updated',
            '0025'  =>  'Confirm password didn\'t match',
            // basic errors
            '0031'  =>  'User is not activated',
            '0032'  =>  'You are not logged in from this device',
            '0033'  =>  'User already active',
            '0034'  =>  'You had requested to reset password.
                       Check your email for further processing',
            // invalid request
            '0051'  =>  'Invalid user',
            '0052'  =>  'Invalid token',
            '0053'  =>  'Invalid request',

            // posts(data) messages
            '0071'  =>   'Post(s) not found',
            '0072'  =>   '%d posts found.',
            '0073'  =>   '%s posted successfully.',
            '0074'  =>   'Unable to store post',

            // file handling
            '0061'  =>   'Unable to write file' ,
            '0062'  =>   'Unable to read file (Invalid filename)',
            '0063'  =>   'Invalid filename',
            '0064'  =>   'Image uploaded successfully',

            );

        return $codes[$code];
    }

    /**
     * @param  string  $code    Status code
     * @param  Request $request Post data
     * @param  array   $errors  Validation errors
     * @return array            array of message, errors and data
     */
    public function validationResponse($code, $request, $errors)
    {
        $response['code']    = $code;
        $response['message'] = $this->code($response['code']);
        $response['errors']  = $errors;
        $response['data']    = $request;
                            
        return $response;
    }


   public function postResponse($code, $posts)
    {
        $response['code']       = $code;
        $response['message']    = $this->code($response['code']);
        $response['post']       = $posts;

        return $response;
    }

    public function __call($name, $arg)
    {
        if ($name == 'userResponse') {
            $arg[0]['message'] = $this->code($arg[0]['code']);
        
            return $arg[0];
        }
    }
}