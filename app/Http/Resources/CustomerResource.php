<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'no_kk' => $this->no_kk,
            // 'dob'=> $this->dob,
            // 'job' => $this->job,
            // 'religion'=> $this->religion,
            // 'family_election' => $this->family_election,
            // 'family_member' => $this->family_member,
            // 'marrital_status' => $this->marrital_status,
            // 'monthly_income' => $this->monthly_income,
           
            'province' => new RegionResource($this->province),
            'city' => new RegionResource($this->city),
            'district' => new RegionResource($this->district),
            'village' => new RegionResource($this->village),
             'surveyor_id' => $this->surveyor_id,
            'statusInterview' => $this->statusInterview,
             
        ];
    }
}
