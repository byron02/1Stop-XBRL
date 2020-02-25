<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientInvoicesRequest extends FormRequest
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
            "company" => "required",
            'client_start_date' => 'required|date_format:Y-m-d|max:255|before:client_end_date',
            'client_end_date' => 'required|date_format:Y-m-d|required|max:255|after:client_start_date',
        ];
    }
}
