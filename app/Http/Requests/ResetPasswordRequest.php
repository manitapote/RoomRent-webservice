<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Common;

class ResetPasswordRequest extends FormRequest
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

    protected $response = array();

    public function rules()
    {
        return [
        'api_token'   => 'required',
        'oldPassword' => 'required',
        'newPassword' => 'required',
        ];
    }

    public function response(array $errors)
    {
        $response['code'] = '0014';
        $response = array_merge($response,[
            'message'=> Common::code($response['code']),
            'errors' => $errors,
            'data' => parent::all(),
            ]);
        return response($response, 400);
    }
}
