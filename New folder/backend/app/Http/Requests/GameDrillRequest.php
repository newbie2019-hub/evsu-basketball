<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameDrillRequest extends FormRequest
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
            'drill' => 'required|min:3',
            'description' => 'required|min:6',
            'instructions' => 'required',
            'drill_category_id' => 'required|exists:drill_categories,id',
            'hours' => 'required',
            'minutes' => 'required',
            'seconds' => 'required',
        ];
    }
}
