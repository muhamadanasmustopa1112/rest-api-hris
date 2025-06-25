<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerjalananDinasResource extends JsonResource
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
            'companies_user_id' => $this->employee->id,
            'company_user' => $this->employee->name,
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'tanggal_pulang' => $this->tanggal_pulang,
            'tujuan' => $this->tujuan,
            'keperluan' => $this->tujuan,
            'rejected_reason' => $this->rejected_reason,
            'status' => $this->status           
        ];    
    }
}
