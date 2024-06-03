<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    
    {
        $statusKunjungan = $this->status_kunjungan === 'Sudah' ? 'Sudah Dikunjungi' : 'Belum Dikunjungi';
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'dob'=> $this->dob,
            'job' => $this->job,
            'no_kk' => $this->no_kk,
            'jenis_kelamin' => $this->jenis_kelamin,
            'religion'=> $this->religion,
            'education'=> $this->education,
            'family_election' => intval($this->family_election),
          'family_member' => intval($this->family_member),
            'marrital_status' => $this->marrital_status,
            'monthly_income' => $this->monthly_income,
            'tps' => $this->tps,
            'no_kk' => $this->no_kk,
            'province' => new RegionResource($this->province),
            'city' => new RegionResource($this->city),
            'district' => new RegionResource($this->district),
            'village' => new RegionResource($this->village),
             'surveyor_id' => $this->surveyor_id,
            'statusInterview' => $this->statusInterview,
            'statusKunjungan' => $statusKunjungan,
            'surveyor' => $this->surveyor,
             
        ];
    }
}
