<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Common;

require app_path().'/validators.php';

class PostRequest extends FormRequest
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
            'title' => 'string|required',
            'post_description' => 'string|required',
            'location' => 'required',
            'latitude' => 'lat_long',
            'longitude' => 'lat_long',
            'price' => 'numeric|required',
            'no_of_rooms' => 'numeric|required',
            'file.*' => 'mimes:jpeg,bmp,png,jpg',
            'offer_or_ask' => 'numeric|required',
        ];
    }

     /**
     * @param  array   $errors  Validation errors
     * @return json             
     */
    public function response(array $errors)
    {
         return response($this->common->message(
                '0014', 
                parent::except('api_token'),
                $errors
             ));
    }
}
