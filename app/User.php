<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

    public function getValidationRules($rule = ''){

        switch ($rule) {
            case 'forgot':
                $forgotPassword = [
                'newPassword' => 'required|confirmed',
                ];
                return $forgotPassword;
            case 'change':
                $changePassword = [
                'api_token' => 'required',
                'oldPassword' => 'required',
                'newPassword' => 'required',
                ];
                return $changePassword;
            default:
                # code...
            break;
        }
    }
}
