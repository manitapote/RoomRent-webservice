<?php

namespace App;

class Common {
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
            '0034' => 'You had requested to reset password. Check your email for further processing',
            // invalid request
            '0051' => 'Invalid user',
            '0052' => 'Invalid token',
            '0053' => 'Invalid request',
        );
        return $codes[$code];
    }
}
