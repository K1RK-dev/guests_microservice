<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuestRequest extends FormRequest
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
            'firstname' => ['required', 'string', 'max:64'],
            'lastname' => ['required', 'string', 'max:64'],
            'phone' => ['required', 'string', 'max:18', 'regex:/^\+?\d{1,3}\d{4,14}$/'],
            'email' => ['email'],
            'country_id' => ['int']
        ];
    }
}
