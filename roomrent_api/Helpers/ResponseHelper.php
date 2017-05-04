<?php

namespace Roomrent\Helpers;

class ResponseHelper
{
    /**
     * @param  string $code status code 
     * @return string message related with status code
     */
    public function code($code) {
        $codes = array(
            // basic messages
            '0000'  =>  'Error occurred',
            '0001'  =>  'Successfully %s',
            '0002'  =>  'Route method not allowed',
            '0003'  =>  'Route you are trying to access is not found',
            '0004'  =>  'Unauthenticated. Validate your api token',

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


            //model related errors
            '0081'  =>    'Record Not Found',
            '0082'  =>    '%s already taken',

            '0091'  =>     'Success',
            );

        return $codes[$code];
    }

    public function __call($name, $arg)
    {
        if ($name == 'jsonResponse') {
            $arg[0]['message'] = $this->code($arg[0]['code']);
        }

        if (count($arg) == 2) {
            $arg[0]['message'] = sprintf($arg[0]['message'], $arg[1]);
        }

        return $arg[0];
        
    }
}