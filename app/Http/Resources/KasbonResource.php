<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KasbonResource extends JsonResource
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
            'nominal' => $this->nominal,
            'tenor' => $this->tenor,
            'keterangan' => $this->keterangan,
            'status' => $this->status           
        ];    
    }
}
