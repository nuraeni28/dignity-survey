<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'address' => ['required', 'string'],
            'nik' => ['required', 'numeric', 'digits:16', 'unique:users,nik,' . $this->user->id],
            'phone' => ['required', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
            'profile_image' => ['nullable','image', 'max:2048'],
        ];
    }
}