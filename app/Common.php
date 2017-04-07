<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Common extends Model {
    static function code($code) {
        $codes = array(
            // basic messages
            '0000' => 'Error occured',
            '0001' => 'Success',
            '0002' => 'Valid Token',

            // login messages
            '0011' => 'Successfully logged in',
            '0012' => 'Login failed. Incorrect email and/or password',

            // registration messages
            '0013' => 'User registered',
            '0014' => 'Validation Errors',
            '0015' => 'Email already exist',
            '0016' => 'User not registered. Unable to send activation mail',
            '0017' => 'Username Already Exist',
            '0018' => 'User Activated',
            '0019' => 'Password doesnot match',

            // profile update messages
            '0021' => 'Old password doesn\'t match',
            '0022' => 'Email address not found',
            '0023' => 'A password reset link has been sent to your nominated email address',

            // basic errors
            '0031' => 'User is inactive',
            '0032' => 'You are not logged in from this device',
            '0033' => 'User already active',


            // invalid request
            '0051' => 'Invalid User',
            '0052' => 'Invalid token',
            '0053' => 'Invalid Request'

           
        );
        return $codes[$code];
    }
}
