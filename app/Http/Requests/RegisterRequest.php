<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Common;

class RegisterRequest extends FormRequest
{
   
    /**
     * variable to bind to Common model
     * @var App\Common
     */
    protected $common ;

    /**
     * @param Common 
     */
    public function __construct(Common $common)
    {
        $this->common = $common;
    }

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
        ];
    }

    /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
        return response($this->common->message('0014', parent::except(['password']), $errors));
    }
}
