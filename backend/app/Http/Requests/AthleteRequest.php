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
        if(request()->routeIs('athletes.store')) {
            return $this->saveRules();
        }

        if(request()->routeIs('athletes.update')) {
            return $this->updateRules();
        }
    }

    public function saveRules()
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['sometimes'],
            'first_name' => ['required', 'max:120'],
            'middle_name' => ['max:100'],
            'last_name' => ['required', 'max:120'],
            'gender' => ['required'],
            'position' => ['required', 'max:50'],
            'address' => ['required'],
            'contact' => ['required'],
        ];
    }

    public function updateRules()
    {
        return [
            'email' => ['required'],
            'first_name' => ['required', 'max:120'],
            'middle_name' => ['max:100'],
            'last_name' => ['required', 'max:120'],
            'gender' => ['required'],
            'position' => ['required', 'max:50'],
            'address' => ['required'],
            'contact' => ['required'],
        ];
    }
}
