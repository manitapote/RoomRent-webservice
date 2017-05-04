<?php

namespace Roomrent\User\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Roomrent\User\Requests\LoginRequest;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @SWG\Definition(
 *     required={"username", "email", "password"},
 *     type="object",
 *     definition="User",
 *     @SWG\Property(property="username", type="string", example="john"),
 *     @SWG\Property(property="email", type="string", example="example@example.com"),
 *     @SWG\Property(property="name", type="string", example="john"),
 *     @SWG\Property(property="password", type="string"),
 *     @SWG\Property(property="phone", type="integer", example="123456")
 * )
 */

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'phone','profileImage', 'name', 
        'activation_token', 'forgot_token', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *  
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'active', 'activation_token', 'updated_at',
        'created_at', 'activation_token', 'forgot_token'
    ];

   /**
    * Gets all offers related to particular user
    * @return array of Post object
    */
   public function posts()
   {
       return $this->hasMany('Roomrent\Posts\Models\Post');
   }
}
