<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactCreateRequest extends FormRequest
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
            'name'  => 'required|regex:/^[a-zA-Z]+$/u|min:1|max:150',
            'email' => 'required|unique:contacts,email|min:1|max:150',
            'phone' => 'required|unique:contacts,phone|min:10|max:13',
        ];
    }
}
