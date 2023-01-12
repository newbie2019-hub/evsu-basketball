<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerPerformanceRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'free_throws_attempted' => 'required',
            'free_throws_made' => 'required',
            'field_goals_attempted' => 'required',
            'field_goals_made' => 'required',
            'three_pointers_attempted' => 'required',
            'three_pointers_made' => 'required',
            'total_assist' => 'required',
            'total_rebound' => 'required',
            'total_steal' => 'required',
            'total_block' => 'required',
        ];
    }
}
