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
            'name' =>  $this->getTranslation('name', $this->language)?? $this->getTranslation('name', 'en'),
            'email' => $this->email,
            'avatar' => $this->getFirstMediaUrl('avatar') ?: null,
            'language' => $this->language,
            'gender' => $this->gender,
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'created_at' => $this->created_at,
        ];
    }
}
