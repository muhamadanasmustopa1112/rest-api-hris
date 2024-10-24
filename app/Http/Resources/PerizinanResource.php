<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerizinanResource extends JsonResource
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
            'jenis_perizinan_id' => $this->jenisIzin->id,
            'category_id' => $this->CategoryIzin->id,
            'companies_user_id' => $this->companyUser->id,
            'company_user' => $this->companyUser->name,
            'jenis_perizinan_name' => $this->jenisIzin->name,
            'category_name' => $this->CategoryIzin->name,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'jam_masuk' => date('H:i', strtotime($this->jam_masuk)),
            'jam_keluar' => date('H:i', strtotime($this->jam_keluar)),
            'keterangan' => $this->keterangan,
            'status' => $this->status, 
            'lampiran' => $this->lampiran ? asset("storage/" . $this->lampiran) : null,
           
        ];
    }
}
