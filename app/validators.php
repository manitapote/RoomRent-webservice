<?php

Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('/^[\pL\s]+$/u', $value);
});

Validator::extend('lat_long', function($attribute, $value)
{
    return preg_match('/^\-?[0-9]{0,3}\.[0-9]{0,8}$/u', $value);
});