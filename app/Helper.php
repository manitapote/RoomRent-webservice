<?php

namespace App;

class Helper
{
	const R = 6371;  //radius of earth

	 /**
     * array to store response code, message, errors or posts
     * @var array
     */
    protected $response = [];

	public static function calculateLatLongRange($distance, $lat, $long)
	{
		$data             = [];
		$r     	 		  = $distance / self::R ;
		$lat  			  = Helper::convertDegreeToRadian($lat);
		$long  			  = Helper::convertDegreeToRadian($long);
		
		$data['lat_max']  = Helper::convertRadianToDegree($lat + $r);
		$data['lat_min']  = Helper::convertRadianToDegree($lat - $r);

		$delLong          = asin(sin($r)/cos($lat));
		
		$data['long_max'] = Helper::convertRadianToDegree($long + $delLong);
		$data['long_min'] = Helper::convertRadianToDegree($long - $delLong);

		return $data; 

	}

	public static function convertDegreeToRadian($degree)
	{
		return ($degree * M_PI / 180);
	}

	public static function convertRadianToDegree($radian)
	{
		return ($radian * 180/ pi());
	}

    /**
     * @param  string $code status code 
     * @return string message related with status code
     */
    static function code($code) {
        $codes = array(
            // basic messages
            '0000' => 'Error occurred',
            '0001' => 'Success',
            // login messages
            '0011' => 'Successfully logged in',
            '0012' => 'Login failed. Incorrect %s and/or password',
            // registration messages
            '0013' => 'User registered',
            '0014' => 'Validation Errors',
            '0015' => 'Email verified',
            '0016' => 'Unable to send mail',
            // logout message
            '0020' => 'Logged out successfully',
            // profile update messages
            '0021' => 'Old password doesn\'t match',
            '0022' => 'Email address not found',
            '0023' => 'A password reset link has been sent to your nominated email address',
            '0024' => 'Password successfully updated',
            '0025' => 'Confirm password didn\'t match',
            // basic errors
            '0031' => 'User is not activated',
            '0032' => 'You are not logged in from this device',
            '0033' => 'User already active',
            '0034' => 'You had requested to reset password.
                       Check your email for further processing',
            // invalid request
            '0051' => 'Invalid user',
            '0052' => 'Invalid token',
            '0053' => 'Invalid request',

            // posts(data) messages
            '0071'  =>  'Post(s) not found',
            '0072'  =>  '%d posts found.',
            '0073'  =>  '%s posted successfully.',
            '0074'  =>  'Unable to store post',

            // file handling
            '0061'  =>  'Unable to write file' ,
            '0062'  =>  'Unable to read file (Invalid filename)',
            '0063'  =>  'Invalid filename',
            '0064'  =>  'Image uploaded successfully',

            );
        return $codes[$code];
    }

    /**
     * @param  string  $code    Status code
     * @param  Request $request Post data
     * @param  array   $errors  Validation errors
     * @return array            array of message, errors and data
     */
    static function message($code, $request, $errors)
    {
        $response['code'] = $code;
        $response = array_merge($response,[
            'message'=> Helper::code($response['code']),
            'errors' => $errors,
            'data' => $request,
            ]);
        return $response;
    }


    static function postResponse($code, $posts)
    {
    	$response['code']      = $code;
    	$response['message']   = Helper::code($response['code']);
		$response['post']      = $posts;
    	return $response;
    }

    public static function __callStatic($name,$arg)
    {
       if($name == 'userResponse'){
         $response['code']      = $arg[0];          //0 = Code
         $response['message']   = Helper::code($response['code']);
         switch (count($arg)){
            case 1 : return $response;
            case 2 :
                $response['uses'] = $arg[1];        //1 = uses
                break;
            case 4 :
                $response['uses'] = $arg[1];    
                $response['user'] = $arg[2];        //2 = user
                $response['api_token'] = $arg[3];   //3 = api_token
                break;
         } 
        }
        return $response;
    }
}