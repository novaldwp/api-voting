<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class CreateCandidateRequest extends FormRequest
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
            'first_name'    => 'required|string',
            'email'         => 'required|email|unique:candidates,email',
            'phone'         => 'required|numeric|min:11|unique:candidates,phone',
            'dob'           => 'required|date',
            'address'       => 'required|min:6',
            'vision'        => 'required',
            'mission'       => 'required',
            'image'         => 'required|image|mimes:jpg,jpeg,png,gif,svg'
        ];
    }
}
