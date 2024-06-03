<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'nik' => $this->nik,
            'phone' => $this->phone,
            'address' => $this->address,
            'tps' => $this->tps,
            'jenis_kelamin' => $this->jenis_kelamin,
            'recomended_by' => $this->recomended_by,
            'verified_at' => $this->email_verified_at,
            'profile_image' => $this->profile_image,
            'province' => new RegionResource($this->province),
            'city' => new RegionResource($this->city),
            'district' => new RegionResource($this->district),
            'village' => new RegionResource($this->village),
            'admin' => new UserResource($this->admin),
            'owner' => new UserResource($this->owner),
        ];
    }
}
