<?php

namespace App\Http\Controllers;
use App\Models\Shift;
use App\Models\PresensiMasuk;
use App\Models\PresensiKeluar;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PresensiResource;
use Illuminate\Support\Carbon;


use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $company_id = $request->input('company_id');
            
            $data = Shift::where('company_id', $company_id)->get();
    
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'status' => 'required|string',
            'company_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $shift = Shift::create([
            'name'=> $request->name,
            'status'=> $request->status,
            'company_id'=> $request->company_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shift = Shift::find($id);

        if($shift == null){
            return response()->json([
                'status' => 'error',
                'data' => $shift,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' => $shift
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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
            ]);
    
            $shift = Shift::findOrFail($id);
    
            $shift->name = $request->input('name');
            $shift->status = $request->input('status');
            $shift->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Shift berhasil diupdate',
                'data' => $shift
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Divisi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui divisi' . $e
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shift = Shift::find($id);

        // Cek apakah divisi ditemukan
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan.'], 404);
        }

        // Hapus divisi
        $shift->delete();

        // Kembalikan respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Shift berhasil dihapus!',
        ], 200);    
    }

    public function getPreseni(Request $request)
    {
        $company_id = $request->input('company_id');

        $data = Shift::with(['presensiMasuk','presensiMasuk.companyUser', 'presensiKeluar'])
        ->where('company_id', $company_id)
        ->get();

        return response()->json([
            'status' => true,
            'data' => PresensiResource::collection($data),
        ], 200);
    }

    public function getShiftActive(Request $request)
    {
        try {

            $company_id = $request->input('company_id');
            
            $data = Shift::where('company_id', $company_id)
                        ->where('status', 'Active')
                        ->get();
    
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found',
            ], 404);
        }
    }

    public function getPresensiEmployee(Request $request)
    {
        $companies_users_id = $request->input('companies_users_id');

        if (!$companies_users_id) {
            return response()->json([
                'status' => false,
                'message' => 'companies_users_id is required',
            ], 400);
        }

        // Ambil semua presensi masuk dan keluar berdasarkan companies_users_id
        $presensiMasuk = PresensiMasuk::with('shift')
            ->where('companies_users_id', $companies_users_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $presensiKeluar = PresensiKeluar::with('shift')
            ->where('companies_users_id', $companies_users_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Gabungkan presensi masuk dan keluar berdasarkan tanggal
        $data = [];

        foreach ($presensiMasuk as $masuk) {
            $keluar = $presensiKeluar->first(function ($item) use ($masuk) {
                return $item->tanggal === $masuk->tanggal;
            });

            $data[] = [
                'tanggal' => $masuk->tanggal,
                'shift' => $masuk->shift->name ?? '-',
                'jam_masuk' => Carbon::parse($masuk->jam)->format('H:i'),
                'jam_keluar' => $keluar ? Carbon::parse($keluar->jam)->format('H:i') : null,
                'status_masuk' => $masuk->status,
                'status_keluar' => $keluar->status ?? 'Missing Clock-Out',
                'keterangan_masuk' => $masuk->keteragan,
                'keterangan_keluar' => $keluar->keteragan ?? null,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

 
}
