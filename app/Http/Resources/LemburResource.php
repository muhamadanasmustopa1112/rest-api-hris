<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LemburResource extends JsonResource
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
            'companies_user_id' => $this->companyUser->id,
            'company_user' => $this->companyUser->name,
            'tanggal' => $this->tanggal,
            'jam' => date('H:i', strtotime($this->jam)),
            'jam_keluar' => date('H:i', strtotime($this->jam_keluar)),
            'total_jam' => $this->total_jam,
            'description' => $this->description,
            'status' => $this->status           
        ];
    }
}
