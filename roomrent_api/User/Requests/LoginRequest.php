<?php

namespace Roomrent\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Roomrent\Helpers\ResponseHelper;

class LoginRequest extends FormRequest
{
    /**
     * Object to bind to Helper class
     * 
     * @var App\Helper
     */
    protected $responsehelper ;

    /**
     * @param Helper
     */
    public function __construct(ResponseHelper $responsehelper)
    {
        $this->responsehelper = $responsehelper;
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
            'identity'      => 'required',
            'password'      => 'required',
            'device_type'   => 'required|numeric',
            'device_token'  => 'required',
        ];
    }

    
    /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
       return response(
           $this->responsehelper->jsonResponse('0014', parent::except(['password']),
           $errors)
       );
    }

}
