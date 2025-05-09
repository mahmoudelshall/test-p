<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' =>  $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile?->mobile,
            'country_code_id' => $this->mobile?->country_code_id,
            'avatar' => $this->getFirstMediaUrl('avatar') ?: null,
            'language' => $this->language,
            'gender' => $this->gender,
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'created_at' => $this->created_at,
        ];
    }
}
