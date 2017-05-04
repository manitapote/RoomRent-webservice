<?php

namespace Roomrent\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Roomrent\Helpers\ResponseHelper;

require app_path().'/validators.php';

class RegisterRequest extends FormRequest
{
    /**
     * Object to inject the ResponseHelper class
     * @var Object
     */
    public $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
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
        $rules = [
            'name'     => 'alpha_spaces',
            'phone'    => 'numeric',
            'username' => 'required|min:5|max:35',

            //'password' => 'required',
            'file'     => 'mimes:jpeg,png,bmp,jpg',
        ];

        if (!auth()->user()) {
            $rules['password'] = 'required';
            $rules['email']    = 'required|email|unique:users,email';
            $rules['phone']    = 'numeric|required';
            $rules['username'] = 'required|min:5|max:35|unique:users,username';
        }

        return $rules;
        // return [
        //     'email'    => 'required|email|unique:users,email',
        //     'name'     => 'alpha_spaces',
        //     'password' => 'required',
        //     'phone'    => 'numeric',
        //     'username' => 'required|min:5|max:35|unique:users,username',
        //     'file'     => 'mimes:jpeg,png,bmp,jpg',
        // ];
    }

    /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
        return response($this->responseHelper->jsonResponse([
            'code'   => '0014', 
            'errors' => $errors,
            'data'   => parent::except(['password']), 
        ]));
    }
}
