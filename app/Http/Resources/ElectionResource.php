<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ElectionResource extends JsonResource
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
            'name'          => $this->name,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'start_datee'   => date('d-m-Y', strtotime($this->start_date)),
            'end_datee'     => date('d-m-Y', strtotime($this->end_date)),
            'thumbnail'     => asset('uploads/images/'. ($this->image == "" ? 'no_image.png':"elections/thumb/".$this->image)),
            'image'         => asset('uploads/images/'. ($this->image == "" ? 'no_image.png':"elections/".$this->image)),
            'status'        => $this->status
        ];
    }
}
