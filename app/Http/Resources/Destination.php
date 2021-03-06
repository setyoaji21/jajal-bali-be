<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Destination extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'detail' => $this->detail,
            'price' => $this->price,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'rating' => $this->rating,
            'picture' => $this->picture,
            'picture_url' => env('APP_URL').$this->picture,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
