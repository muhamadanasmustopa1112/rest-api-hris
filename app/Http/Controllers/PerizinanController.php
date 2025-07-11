<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Perizinan;
use App\Http\Resources\PerizinanResource;

class PerizinanController extends Controller
{
    public function index(Request $request)
    {
        try {

            $company_id = $request->input('company_id');
            
            $data = Perizinan::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->get();

            return response()->json([
                'success' => true,
                'data' => PerizinanResource::collection($data),
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perizinan not found',
            ], 404);
        }

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
      
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'jenis_izin_id' => 'required|exists:jenis_izin,id',
            'category_id' => 'required|exists:category_izin,id',
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'keterangan' => 'required|string',
            'status' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
       $lampiran = $request->file('lampiran') ? $request->file('lampiran')->store('uploads/lampiran', 'public') : null;

        $perizinan = Perizinan::create([
            'jenis_izin_id'=> $request->jenis_izin_id,
            'category_id' => $request->category_id,
            'companies_users_id' => $request->companies_users_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'lampiran' => $lampiran,
       
        ]);
 

        return response()->json([
            'message' => 'success',
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Perizinan::find($id);

        if (!$data) {
            return response()->json(['message' => 'Perizinan User not found'], 404);
        }
    
        return new PerizinanResource($data);
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
        //
        $validator = Validator::make($request->all(), [
            'jenis_izin_id' => 'required|exists:jenis_izin,id',
            'category_id' => 'required|exists:category_izin,id',
            'companies_user_id' => 'required|exists:companies_users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'keterangan' => 'required|string',
            'status' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        $perizinan = Perizinan::find($id);

        if (!$perizinan) {
            return response()->json(['message' => 'Perizinan User not found'], 404);
        }

        $perizinan->update($validator->validated());

        if ($request->hasFile('lampiran')) {
            $fileName = time() . '_' . $request->file('lampiran')->getClientOriginalName();
            $filePath = $request->file('lampiran')->storeAs('uploads/lampiran', $fileName, 'public');
            $perizinan->lampiran = $filePath;
        }

        $perizinan->save();

        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $perizinan = Perizinan::findOrFail($id); 
            $perizinan->delete(); 
            
            return response()->json([
                'success' => true,
                'message' => 'Perizinan deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perizinan not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Perizinan',
            ], 500);
        }
    }

    public function getPerizinanWhereCompanyUser($id)
    {
        try {
           $data = Perizinan::where('companies_users_id', $id)->get();

            return response()->json([
                'success' => true,
                'data' => PerizinanResource::collection($data),
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Perizinan not found',
            ], 404);
        }
    }

    public function getPerizinanSummary($id)
    {
        try {
            $pending = Perizinan::where('companies_users_id', $id)
                ->where('status', 'pending')->count();

            $approved = Perizinan::where('companies_users_id', $id)
                ->where('status', 'approved')->count();

            $rejected = Perizinan::where('companies_users_id', $id)
                ->where('status', 'rejected')->count();

            $totalLeave = 12;
            $usedLeave = $approved;
            $remainingLeave = $totalLeave - $usedLeave;

            return response()->json([
                'success' => true,
                'data' => [
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'leave_balance' => $remainingLeave,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary perizinan',
            ], 500);
        }
    }

    public function getPerizinanSummaryByCompany($company_id)
    {
        try {
            $pending = Perizinan::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'pending')->count();

            $approved = Perizinan::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'approved')->count();

            $rejected = Perizinan::whereHas('companyUser.company', function ($query) use ($company_id) {
                $query->where('id', $company_id);
            })->where('status', 'rejected')->count();

            $totalLeave = 12;
            $usedLeave = $approved;
            $leaveBalance = null; 

            return response()->json([
                'success' => true,
                'data' => [
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'leave_balance' => $leaveBalance,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary perizinan HRD',
            ], 500);
        }
    }

}
