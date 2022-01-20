<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUpdateRequest extends FormRequest
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
            'name'  => 'required|min:1|max:150',
            'email' => 'required|email|min:1|max:150|unique:contacts,email,'.$this->id.',id',
            'phone' => 'required|min:10|max:12|unique:contacts,phone,'.$this->id.',id',
        ];
    }
}
