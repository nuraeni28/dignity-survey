<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EvidenceComitmentResource extends JsonResource
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
            'id' => $this->id,
            'id_customer' => intval($this->id_customer),
            'id_surveyor' => intval($this->id_surveyor),
            'photo' => $this->photo,
             'location' => $this->location,
            'long' => $this->long,
            'lat' => $this->lat,
            'no_kk' =>$this->no_kk,
        ];
    }
}
