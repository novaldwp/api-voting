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
            'name'          => 'required|string|min:4',
            'start_date'    => 'required|date|after:today',
            'end_date'      => 'required|date|after:start_date',
            'image'         => 'required|image|mimes:jpg,png,jpeg,gif,svg',
            'candidate_id'  => 'required|exists:candidates,id'
        ];
    }
}
