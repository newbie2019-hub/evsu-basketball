<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AthleteRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['sometimes'],
            'first_name' => ['required', 'max:120'],
            'middle_name' => ['max:100'],
            'last_name' => ['required', 'max:120'],
            'position' => ['required', 'max:50'],
            'address' => ['required'],
            'contact' => ['required'],
        ];
    }
}
