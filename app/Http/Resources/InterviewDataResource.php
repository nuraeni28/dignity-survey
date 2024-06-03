<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewDataResource extends JsonResource
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
            'question' => $this->question,
            'question_id' => $this->question_id,
            'type' => $this->type,
            'highlight' => $this->highlight,
            'answer' => $this->answer,
            'customer_answer' => $this->customer_answer,
        ];
    }
}