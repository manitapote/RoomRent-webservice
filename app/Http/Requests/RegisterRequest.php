<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helper;

class RegisterRequest extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'email'    => 'required|email|unique:users,email',
        'name'     => 'alpha',
        'password' => 'required',
        'phone'    => 'numeric',
        'username' => 'required|min:5|max:35|unique:users,username',
        'file'     => 'mimes:jpeg,png,bmp,jpg',
        ];
    }

    /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
        return response(Helper::message('0014', parent::except(['password']), $errors));
    }
}
