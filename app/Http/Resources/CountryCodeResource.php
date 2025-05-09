<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryCodeResource extends JsonResource
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
            'iso' => $this->iso,
            'name' => $this->name,
            'phonecode' => $this->phonecode,
            'display' => $this->name . ' (' . $this->iso . ')',
        ];
    }
}
