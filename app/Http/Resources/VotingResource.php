<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VotingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'created_at'    => $this->created_at,
            'created_att'   => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'users'         => $this->users,
            'candidates'    => $this->candidates,
            'elections'     => $this->elections
        ];
    }
}
