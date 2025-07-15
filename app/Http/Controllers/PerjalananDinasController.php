<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use Illuminate\Support\Carbon;

class PerjalananDinasController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::with(['company', 'employee']);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('employee_id')) {
            $query->where('companies_users_id', $request->employee_id);
        }

        $results = $query->get();

        return response()->json([
            'data' => $results
        ]);
    }

    public function show($id)
    {
        return PerjalananDinas::with(['company', 'employee'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'required|date|after_or_equal:tanggal_berangkat',
            'tujuan' => 'required|string',
            'keperluan' => 'required|string',
        ]);

        $validated['status'] = 'pending';

        $data = PerjalananDinas::create($validated);

        return response()->json([
            'message' => 'Perjalanan dinas berhasil dibuat',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $dinas = PerjalananDinas::findOrFail($id);

        $validated = $request->validate([
            'tanggal_berangkat' => 'sometimes|date',
            'tanggal_pulang' => 'sometimes|date|after_or_equal:tanggal_berangkat',
            'tujuan' => 'sometimes|string',
            'keperluan' => 'sometimes|string',
            'status' => 'in:pending,approved,rejected',
            'rejected_reason' => 'nullable|string',
        ]);

        $dinas->update($validated);

        return response()->json([
            'message' => 'Perjalanan dinas berhasil diperbarui',
            'data' => $dinas
        ]);
    }

    public function destroy($id)
    {
        $dinas = PerjalananDinas::findOrFail($id);
        $dinas->delete();

        return response()->json([
            'message' => 'Perjalanan dinas berhasil dihapus'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $perjalanan_dinas = PerjalananDinas::find($id);

        if (!$perjalanan_dinas) {
            return response()->json([
                'success' => false,
                'message' => 'perjalanan_dinas tidak ditemukan',
            ], 404);
        }

        $perjalanan_dinas->status = $request->status;
        $perjalanan_dinas->save();

        return response()->json([
            'success' => true,
            'message' => 'Status perjalanan_dinas berhasil diperbarui',
        ]);
    }

    public function getPerjalananDinasSummaryByCompany($company_id)
    {
        try {
            $pending = PerjalananDinas::where('company_id', $company_id)
                ->where('status', 'pending')
                ->count();

            $approved = PerjalananDinas::where('company_id', $company_id)
                ->where('status', 'approved')
                ->count();

            $rejected = PerjalananDinas::where('company_id', $company_id)
                ->where('status', 'rejected')
                ->count();

            $returned = PerjalananDinas::where('company_id', $company_id)
                ->where('status', 'return')
                ->count();

            $total = PerjalananDinas::where('company_id', $company_id)->count();

            // Hitung total hari dari perjalanan dinas yang disetujui
            $approvedTrips = PerjalananDinas::where('company_id', $company_id)
                ->where('status', 'approved')
                ->get();

            $totalDaysApproved = $approvedTrips->sum(function ($trip) {
                $start = Carbon::parse($trip->tanggal_berangkat);
                $end = Carbon::parse($trip->tanggal_pulang);
                return $start->diffInDays($end) + 1;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_pengajuan' => $total,
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'return' => $returned,
                    'total_hari_disetujui' => $totalDaysApproved,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary perjalanan dinas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPerjalananDinasSummaryByCompanyUser($companies_users_id)
    {
        try {
            $pending = PerjalananDinas::where('companies_users_id', $companies_users_id)
                ->where('status', 'pending')
                ->count();

            $approved = PerjalananDinas::where('companies_users_id', $companies_users_id)
                ->where('status', 'approved')
                ->count();

            $rejected = PerjalananDinas::where('companies_users_id', $companies_users_id)
                ->where('status', 'rejected')
                ->count();

            $returned = PerjalananDinas::where('companies_users_id', $companies_users_id)
                ->where('status', 'return')
                ->count();

            $total = PerjalananDinas::where('companies_users_id', $companies_users_id)->count();

            // Hitung total hari dari perjalanan dinas yang disetujui
            $approvedTrips = PerjalananDinas::where('companies_users_id', $companies_users_id)
                ->where('status', 'approved')
                ->get();

            $totalDaysApproved = $approvedTrips->sum(function ($trip) {
                $start = Carbon::parse($trip->tanggal_berangkat);
                $end = Carbon::parse($trip->tanggal_pulang);
                return $start->diffInDays($end) + 1;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_pengajuan' => $total,
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'return' => $returned,
                    'total_hari_disetujui' => $totalDaysApproved,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary perjalanan dinas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
