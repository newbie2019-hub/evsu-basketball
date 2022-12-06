<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['sometimes', Password::min(8)],
            'first_name' => ['required', 'max:120'],
            'last_name' => ['required', 'max:120'],
            'middle_name' => ['sometimes', 'max:100'],
            'year' => ['sometimes'],
            'section' => ['sometimes'],
            'course' => ['sometimes'],
            'position' => ['sometimes'],
            'contact' => ['required'],
            'address' => ['required'],
            'gender' => ['required', 'max:7'],
            'height' => ['required'],
            'weight' => ['required'],
            'date_of_birth' => ['required'],
        ];
    }
}
