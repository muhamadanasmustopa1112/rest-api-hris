<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserResource extends JsonResource
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
            'nik' => $this->nik,
            'no_kk' => $this->no_kk,
            'name' => $this->name,
            'gender' => $this->gender,
            'tgl_lahir' => $this->tgl_lahir,
            'tempat_lahir' => $this->tempat_lahir,
            'no_hp' => $this->no_hp,
            'email' => $this->email,
            'no_hp_darurat' => $this->no_hp_darurat, 
            'status' => $this->status, 
            'alamat' => $this->alamat,
            'bpjs_kesehatan' => $this->bpjs_kesehatan,
            'bpjs_ketenagakerjaan' => $this->bpjs_ketenagakerjaan,
            'npwp' => $this->npwp,
            'foto_karyawan' =>  $this->foto_karyawan ? asset("storage/"  . $this->foto_karyawan) : null,
            'ktp_karyawan' => $this->ktp_karyawan ? asset("storage/"  . $this->ktp_karyawan) : null,
            'ijazah_karyawan' => $this->ijazah_karyawan ? asset("storage/" . $this->ijazah_karyawan) : null,
            'company_id' => $this->company_id,
            'division_id' => $this->division_id,
            'jabatan_id' => $this->jabatan_id,
            'company' => $this->company->name, 
            'division' => $this->division->name,
            'jabatan' => $this->jabatan->name,
            'qr_code' => asset("storage/" .$this->qr_code),
        ];
    }
}
