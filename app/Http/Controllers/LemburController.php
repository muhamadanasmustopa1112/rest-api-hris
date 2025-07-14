<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LemburResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class LemburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company_id = $request->input('company_id');
        
        $data = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
            $query->where('id', $company_id);
        })->get();

        return response()->json([
            'success' => true,
            'data' => LemburResource::collection($data),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
         try {
            $validator = Validator::make($request->all(), [
                'companies_users_id' => 'required|exists:companies_users,id',
                'tanggal' => 'required|date',
                'jam' => 'required|date_format:H:i',
                'jam_keluar' => 'required|date_format:H:i|after:jam',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $jamMasuk = Carbon::createFromFormat('H:i', $request->jam);
            $jamKeluar = Carbon::createFromFormat('H:i', $request->jam_keluar);
            $durasi = abs($jamMasuk->diffInMinutes($jamKeluar)) / 60;

            $lembur = Lembur::create([
                'companies_users_id' => $request->companies_users_id,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'jam_keluar' => $request->jam_keluar,
                'total_jam' => $durasi,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Lembur berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal simpan lembur: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lembur = Lembur::find($id);

        if(!$lembur){
            return response()->json([
                'status' => 'error',
                'data' => $lembur,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' =>  new LemburResource($lembur),
                'message' => 'Success get data lembur'
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         
         $validator = Validator::make($request->all(), [
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'description' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        $lembur = Lembur::find($id);

        if (!$lembur) {
            return response()->json(['message' => 'Lembur User not found'], 404);
        }

        $lembur->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data Lembur berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lembur = Lembur::findOrFail($id); 
            $lembur->delete(); 
            
            return response()->json([
                'success' => true,
                'message' => 'lembur deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'lembur not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lembur',
            ], 500);
        }
    }

    public function getLemburWhereCompanyUser($id)
    {
        try {
           $data = Lembur::where('companies_users_id', $id)->get();

            return response()->json([
                'success' => true,
                'data' => LemburResource::collection($data),
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lembur not found',
            ], 404);
        }
    }

    public function summary($companyUserId)
    {
        try {
            $total = Lembur::where('companies_users_id', $companyUserId)->count();

            $approved = Lembur::where('companies_users_id', $companyUserId)
                ->where('status', 'approved')
                ->count();

            $pending = Lembur::where('companies_users_id', $companyUserId)
                ->where('status', 'pending')
                ->count();

            $rejected = Lembur::where('companies_users_id', $companyUserId)
                ->where('status', 'rejected')
                ->count();

            $totalApprovedJam = Lembur::where('companies_users_id', $companyUserId)
                ->where('status', 'approved')
                ->sum('total_jam');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil summary lembur',
                'data' => [
                    'total_pengajuan' => $total,
                    'total_pending' => $pending,
                    'total_approved' => $approved,
                    'total_rejected' => $rejected,
                    'total_jam_lembur_disetujui' => round($totalApprovedJam, 2),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil summary lembur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLemburSummaryByCompany($company_id)
    {
        try {
            $pending = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'pending')->count();

            $approved = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'approved')->count();

            $rejected = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'rejected')->count();

            // Total semua lembur
            $total = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->count();

            // Total jam lembur yang disetujui
            $totalApprovedJam = Lembur::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'approved')->sum('total_jam');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_pengajuan' => $total,
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'total_jam_lembur_disetujui' => round($totalApprovedJam, 2),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary lembur HRD',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
