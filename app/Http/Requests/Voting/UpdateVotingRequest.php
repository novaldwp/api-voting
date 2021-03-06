<?php

namespace App\Http\Requests\Voting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVotingRequest extends FormRequest
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
            'user_id'       => 'required|exists:users,id',
            'candidate_id'  => 'required|exists:candidates,id',
            'election_id'   => 'required|exists:elections,id'
        ];
    }
}
