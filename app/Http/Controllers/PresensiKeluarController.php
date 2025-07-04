<?php

namespace App\Http\Controllers;
use App\Models\PresensiKeluar;
use App\Models\Jam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PresensiKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'shift_id' => 'required|exists:shift,id',
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal' => 'required|date',
            'jam' => 'required|',
            'latitude' => 'required|string',
            'longtitude' => 'required|string',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);   
        }
        
        $presensiKeluar = PresensiKeluar::create([
            'shift_id' => $request->shift_id,
            'companies_users_id' => $request->companies_users_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude,
            'status' => $request->status,
            'keteragan' => $request->keterangan,
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
        //
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $presensiKeluar = PresensiKeluar::find($id);

        if (!$presensiKeluar) {
            return response()->json(['message' => 'Presensi Keluar tidak ditemukan.'], 404);
        }

        $presensiKeluar->delete();

        // Kembalikan respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Presensi Keluar berhasil dihapus!',
        ], 200);    
    }
}
