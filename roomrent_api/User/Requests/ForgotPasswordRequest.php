<?php

namespace Roomrent\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'newPassword' => 'required|confirmed',
        ];
    }

    /**
     * Response when validation fails
     * 
     * @param  array  $errors 
     * @return View
     */
    public function response(array $errors)
    {
       $data['token'] = parent::only('token')['token'];
       $data['error'] = $errors['newPassword'];
       
       return response()->view('forgotPasswordForm', compact('data'));
    }
}
