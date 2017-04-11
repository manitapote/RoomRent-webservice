<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
    	'api_token', 'device_type', 'device_token','user_id',
    	];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
