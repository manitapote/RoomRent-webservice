<?php

namespace Roomrent\Posts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Roomrent\Helpers\ResponseHelper;

require app_path().'/validators.php';

class PostRequest extends FormRequest
{
    /**
     * Object to bind to ResponseHelper class
     * @var Roomrent\Helpers\ResponseHelper
     */
    protected $responseHelper ;

    /**
     * @param ResponseHelper
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
            'title'            => 'string|required',
            'post_description' => 'string|required',
            'location'         => 'required',
            'latitude'         => 'lat_long|required',
            'longitude'        => 'lat_long|required',
            'price'            => 'numeric|required',
            'no_of_rooms'      => 'numeric|required',
            'file.*'           => 'mimes:jpeg,bmp,png,jpg',
            'offer_or_ask'     => 'numeric|required',
        ];
    }

     /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
         return response($this->responseHelper->jsonResponse([
            'code' => '0014',
            'data' => parent::except('api_token'),
            'errors' => $errors
        ]));
    }
}
