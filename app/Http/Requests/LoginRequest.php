<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Common;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $respone =  [];

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

    public function response(array $errors)
    {
        $response['code'] = '0014';
        $response['message'] = Common::code($response['code']);

        return response(['code' => '0014','message'=>'Validation error', 'error'=>$errors,'data'=>parent::except(['password'])] , '400');
    }

}
