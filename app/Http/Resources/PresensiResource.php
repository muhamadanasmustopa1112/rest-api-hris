<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use GuzzleHttp\Client;

class PresensiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ambil data untuk presensi masuk
        $presensiMasuk = [];
        foreach ($this->presensiMasuk as $masuk) {
            $presensiMasuk[] = [
                'id' => $masuk->id,
                'companies_user_id' => $masuk->companyUser->id,
                'companies_user' => $masuk->companyUser->name,
                'shift_name' => $this->name,
                'tanggal' => $masuk->tanggal,
                'jam_masuk' => date('H:i', strtotime($masuk->jam)),
                'status_masuk' => $masuk->status,
                'keterangan_masuk' => $masuk->keteragan,
                'alamat_masuk' => $this->getAddressFromCoordinates($masuk->latitude, $masuk->longtitude),
            ];
        }

        // Ambil data untuk presensi keluar
        $presensiKeluar = [];
        foreach ($this->presensiKeluar as $keluar) {
            $presensiKeluar[] = [
                'presensi_keluar_id' => $keluar->id,
                'companies_user_id' => $keluar->companies_users_id,
                'tanggal' => $keluar->tanggal,
                'jam_keluar' => date('H:i', strtotime($keluar->jam)),
                'status_keluar' => $keluar->status,
                'keterangan_keluar' => $keluar->keteragan,
                'alamat_keluar' => $this->getAddressFromCoordinates($keluar->latitude, $keluar->longtitude),
            ];
        }

        // Gabungkan data masuk dan keluar berdasarkan companies_user_id dan tanggal
        $combinedData = [];
        foreach ($presensiMasuk as $masuk) {
            $keluar = collect($presensiKeluar)->first(function ($k) use ($masuk) {
                return $k['companies_user_id'] == $masuk['companies_user_id'] &&
                    $k['tanggal'] == $masuk['tanggal'];
            });

            $combinedEntry = array_merge($masuk, $keluar ?? []);

            if (!empty($combinedEntry['companies_user_id'])) {
                $combinedData[] = [
                    'presensi' => $combinedEntry,
                ];
            }
        }

        return [
            'data' => array_values($combinedData),
        ];
    }

    /**
     * Ambil alamat dari koordinat menggunakan Nominatim API.
     */
    private function getAddressFromCoordinates($latitude, $longitude)
    {
        $client = new Client();

        // Log koordinat yang diambil
        \Log::info("Fetching address for Latitude: {$latitude}, Longitude: {$longitude}");

        try {
            $response = $client->get('https://nominatim.openstreetmap.org/reverse', [
                'query' => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'addressdetails' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'My Absensi/1.0',
                ],
            ]);

            // Log respons dari Nominatim API
            \Log::info("Nominatim Response: " . $response->getBody());

            $data = json_decode($response->getBody(), true);

            if (isset($data['address'])) {
                // Format alamat sebagai string yang dapat dibaca
                return implode(', ', [
                    $data['address']['road'] ?? '',
                    $data['address']['village'] ?? '',
                    $data['address']['subdistrict'] ?? '',
                    $data['address']['city'] ?? '',
                    $data['address']['state'] ?? '',
                    $data['address']['postcode'] ?? '',
                    $data['address']['country'] ?? ''
                ]);
            } else {
                \Log::error("No address found in Nominatim response");
            }
        } catch (\Exception $e) {
            // Log pesan kesalahan jika ada masalah
            \Log::error("Error fetching address: " . $e->getMessage());
            return 'Unable to retrieve address';
        }

        return 'Unknown address';
    }
}
