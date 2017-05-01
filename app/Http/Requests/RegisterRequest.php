<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helper;

require app_path().'/validators.php';

class RegisterRequest extends FormRequest
{
    /**
     * Object to inject the Helper class
     * @var Object
     */
    public $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
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
            'name'     => 'alpha_spaces',
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
        return response($this->helper->validationResponse(
            '0014', 
            parent::except(['password']), 
            $errors
        ));
    }
}
