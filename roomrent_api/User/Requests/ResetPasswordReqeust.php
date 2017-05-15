<?php

namespace Roomrent\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Roomrent\Helpers\ResponseHelper;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Object to bind to Helper class
     * @var App\Helper
     */
    protected $responseHelper ;

    /**
     * Constructor
     * @param Helper $helper 
     */
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
        return [
        'oldPassword' => 'required',
        'newPassword' => 'required',
        ];
    }

    /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
        return response($this->responseHelper->jsonResponse(
            '0014',
            parent::except(['password']), 
            $errors
        ));
    }
}
