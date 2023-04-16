<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

class ClientRequest extends FormRequest
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
            'client_types_id' => 'required',
            'name'               => 'required|string',
            'phone_no'           => 'required',
            'phone_no_1'         => 'nullable',
            'citizenship_no'     => 'required_without_all:passport_no,license_no',
            'passport_no'        => 'required_without_all:license_no,citizenship_no',
            'license_no'         => 'required_without_all:citizenship_no,passport_no',
            'permanent_address'  => 'required|string',
            'temporary_address'  => 'nullable|string',
            // ! this validation for client document
            'citizenship'        => 'required|mimes:pdf,png,jpeg,jpg|max:5048',
            'pan'               => 'nullable|mimes:pdf,png,jpeg,jpg|max:5048',
            'passport'          => 'nullable|mimes:pdf,png,jpeg,jpg|max:5048',
            'photo'             => 'required|mimes:pdf,png,jpeg,jpg|max:5048',
        ];
    }
}
