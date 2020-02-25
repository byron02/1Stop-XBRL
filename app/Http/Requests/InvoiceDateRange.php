<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceDateRange extends FormRequest
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

            'date_range_start_date' => 'required|date_format:Y-m-d|max:255|before:date_range_end_date',
            'date_range_end_date' => 'required|date_format:Y-m-d|max:255|after:date_range_start_date',
        ];
    }
}
