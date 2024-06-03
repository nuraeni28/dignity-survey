<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewScheduleResource extends JsonResource
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
             'type' => $this->type,
            'interview_date' => $this->interview_date,
            'period' => new PeriodResource($this->period),
            'user' => new UserResource($this->user),
            'customer' => new CustomerResource($this->customer),
            'interview' => new InterviewResource($this->interview),
        ];
    }
}
