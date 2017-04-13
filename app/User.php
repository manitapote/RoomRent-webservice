<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Device;
use App\Http\Requests\LoginRequest;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'username', 'email', 'password', 'phone','profileImageURL', 'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *  
     * @var array
     */
    protected $hidden = [
    'password', 'remember_token', 'active', 'activation_token', 'updated_at', 'forgot_token'
    ];
    
    public function storeDevice(LoginRequest $request)
    {
       $device = Device::updateOrCreate([
        'device_token' => $request->device_token, 
        'device_type' => $request->device_type,
        'user_id' => $request->user_id,
        ],[
        'api_token' =>str_random(60)
        ]);
       return $device;
   }
}




