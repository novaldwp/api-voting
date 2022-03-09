<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ElectionResource;

class CandidateResource extends JsonResource
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
            'full_name'     => $this->first_name . ' ' . $this->last_name,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'dob'           => $this->dob,
            'dobb'          => date('d-m-Y', strtotime($this->dob)),
            'address'       => $this->address,
            'vision'        => $this->vision,
            'mission'       => $this->mission,
            'thumbnail'     => asset('uploads/images/'. ($this->image == "" ? 'no_image.png':"candidates/thumb/".$this->image)),
            'image'         => asset('uploads/images/'. ($this->image == "" ? 'no_image.png':"candidates/".$this->image)),
        ];
    }
}
