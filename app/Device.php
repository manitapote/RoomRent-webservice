<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
	/**
	 * Mass fillable fields
	 * @var array
	 */
    protected $fillable = [
    	'api_token', 'device_type', 'device_token','user_id',
    	];

    /**
     * Gets the user associated with the particuler device
     * 
     * @return User 
     */
    public function user()
    {
    	return $this->belongsTo('App\User');
    }

  
}
