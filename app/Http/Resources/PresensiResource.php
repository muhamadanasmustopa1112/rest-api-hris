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
                'alamat' => $this->getAddressFromCoordinates($masuk->latitude, $masuk->longtitude),
            ];
        }

        // Ambil data untuk presensi keluar
        $presensiKeluar = [];
        foreach ($this->presensiKeluar as $keluar) {
            $presensiKeluar[] = [
                'presensi_keluar_id' => $keluar->id,
                'jam_keluar' => date('H:i', strtotime($keluar->jam)),
                'status_keluar' => $keluar->status,
                'keterangan_keluar' => $keluar->keteragan,
            ];
        }

        // Gabungkan data masuk dan keluar
        $combinedData = [];
        foreach ($presensiMasuk as $masuk) {
            // Temukan data keluar yang sesuai jika ada
            $keluar = array_filter($presensiKeluar, function ($k) use ($masuk) {
                return $k['presensi_keluar_id'] == $masuk['id'];
            });

            // Ambil data keluar pertama jika ada
            $keluar = reset($keluar); // Ambil elemen pertama dari array

            // Gabungkan data masuk dan keluar
            $combinedEntry = array_merge($masuk, $keluar ? $keluar : []);

            // Hanya tambahkan ke combinedData jika ada data presensi masuk
            if (!empty($combinedEntry['companies_user_id'])) {
                $combinedData[] = [
                    'presensi' => $combinedEntry, 
                ];
            }
        }

        return [
            'data' => array_values(array_filter($combinedData)),
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
