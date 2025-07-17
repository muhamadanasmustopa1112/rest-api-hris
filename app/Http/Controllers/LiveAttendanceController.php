<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveAttendance;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Carbon;


class LiveAttendanceController extends Controller
{
    public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'companies_users_id' => 'required|exists:companies_users,id',
            'perjalanan_dinas_id' => 'required|exists:perjalanan_dinas,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'latitude_masuk' => 'required|string',
            'longtitude_masuk' => 'required|string',
            'keteragan_masuk' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);   
        }

        $existingRecord = LiveAttendance::where('companies_users_id', $request->companies_users_id)
        ->where('tanggal', $request->tanggal)
        ->first();

        if ($existingRecord) {
            return response()->json([
                'status' => false,
                'errors' => 'Anda sudah melakukan absensi hari ini.',
            ], 422);
        }

        
        $presensiMasuk = LiveAttendance::create([
            'companies_users_id' => $request->companies_users_id,
            'perjalanan_dinas_id' => $request->perjalanan_dinas_id,
            'tanggal' => $request->tanggal,
            'jam_masuk' => $request->jam_masuk,
            'latitude_masuk' => $request->latitude_masuk,
            'longtitude_masuk' => $request->longtitude_masuk,
            'keteragan_masuk' => $request->keteragan_masuk,
        ]);

        if ($presensiMasuk) {
            $user = User::where('companies_users_id', $request->companies_users_id)->first();

            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Live Attendance',
                    'body' => 'You have successfully Live Attendance on ' . Carbon::parse($request->tanggal)->format('D, d F Y') . ' at ' . Carbon::createFromFormat('H:i', $request->jam_masuk)->format('h:i A'),
                    'type' => 'attendance',
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Live Attendance berhasil dibuat.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Gagal Attendance presensi.',
            ], 500);
        }
    }

    public function updateKeluar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal' => 'required|date',
            'jam_keluar' => 'required',
            'latitude_keluar' => 'required|string',
            'longtitude_keluar' => 'required|string',
            'keteragan_keluar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = LiveAttendance::where('companies_users_id', $request->companies_users_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'errors' => 'Data absensi masuk tidak ditemukan untuk update keluar.',
            ], 404);
        }

        $record->update([
            'jam_keluar' => $request->jam_keluar,
            'latitude_keluar' => $request->latitude_keluar,
            'longtitude_keluar' => $request->longtitude_keluar,
            'keteragan_keluar' => $request->keteragan_keluar,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Absensi keluar berhasil diperbarui.',
        ]);
    }

    public function getByUser($companies_users_id)
    {
        $data = LiveAttendance::with(['companyUser', 'perjalananDinas'])
            ->where('companies_users_id', $companies_users_id)
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan untuk user ini.'
            ], 404);
        }

        $formatted = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'jam_masuk' => $item->jam_masuk,
                'jam_keluar' => $item->jam_keluar,
                'nama_user' => optional($item->companyUser)->name,
                'perjalanan_dinas_tujuan' => optional($item->perjalananDinas)->tujuan,
                'perjalanan_dinas_status' => optional($item->perjalananDinas)->status,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formatted
        ]);
    }


    public function getByCompany($company_id)
    {
        $data = LiveAttendance::with([
            'companyUser',          
            'perjalananDinas'       
        ])
        ->whereHas('companyUser', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        })
        ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan untuk company ini.'
            ], 404);
        }

        $formatted = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'jam_masuk' => $item->jam_masuk,
                'jam_keluar' => $item->jam_keluar,
                'nama_user' => optional($item->companyUser)->name,
                'perjalanan_dinas_tujuan' => optional($item->perjalananDinas)->tujuan,
                'perjalanan_dinas_status' => optional($item->perjalananDinas)->status,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formatted
        ]);
    }


}
