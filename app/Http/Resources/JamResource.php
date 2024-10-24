<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JamResource extends JsonResource
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
            'shift_id' => $this->shift->id,
            'shift_name' => $this->shift->name,
            'jam_masuk' => date('H:i', strtotime($this->jam_masuk)),
            'jam_keluar' => date('H:i', strtotime($this->jam_keluar)),
        ];
    }
}
