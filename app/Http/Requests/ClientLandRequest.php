<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientLandRequest extends FormRequest
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
            'client_id'     => 'required',
            'location'       => 'required|string',
            'kitta'          => 'required|string',
            'area'           => 'required|string',
            'price_per_area' => 'required|string',
            //  for land document
            'document'       => 'required|array|min:1',
            'document.*' => 'required|mimes:pdf,png,jpeg,jpg|max:5048',
        ];
    }
}
