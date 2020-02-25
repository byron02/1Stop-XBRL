<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobIdsRequest extends FormRequest
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

        $rules = [

        ];

        foreach($this->request->get('job_ids') as $key => $val)
        {
            $rules['job_ids.'.$key] = 'required|integer';
        }

        return $rules;

//        return [
//            'job_ids'=>'required|array',
//            'job_ids.*' => 'integer'
//        ];
    }
}
