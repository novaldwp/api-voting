<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;

class CreateElectionRequest extends FormRequest
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
            'name' => 'required|string|min:4',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'image' => 'required|image|max:2048'
        ];
    }
}
