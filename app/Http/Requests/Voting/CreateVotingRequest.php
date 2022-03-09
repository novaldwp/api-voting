<?php

namespace App\Http\Requests\Voting;

use Illuminate\Foundation\Http\FormRequest;

class CreateVotingRequest extends FormRequest
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
            'candidate_id'  => 'required|exists:candidates,id',
            'election_id'   => 'required|exists:elections,id'
        ];
    }
}
