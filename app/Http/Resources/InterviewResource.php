<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
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
            'interview_date' => $this->interview_date,
            'photo' => $this->photo,
            'location' => $this->location,
            'long' => $this->long,
            'lat' => $this->lat,
            'data' => InterviewDataResource::collection($this->data),
        ];
    }
}
