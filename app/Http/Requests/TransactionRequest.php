<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'client_id'   => 'required',
            'land_id'     =>'required',
            'nepali_date' =>'required|string',
            'type'        =>'in:income,expenses',
            'income'     =>'required_without:expenses',
            'expenses'    =>'required_without:income',
            'total_paid_amount' => 'required',
            'commission_rate' =>'required',
            'photo'      => 'mimes:pdf,png,jpeg,jpg|max:5048',
            'descriptions' => 'required|max:200',
            'ischeque' => 'boolean|required',
        ];
    }
}
