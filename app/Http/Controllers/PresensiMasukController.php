<?php

namespace App\Http\Controllers;
use App\Models\PresensiMasuk;
use App\Models\Jam;
use Illuminate\Support\Facades\Validator;
// use App\Http\Resources\PresensiResource;

use Illuminate\Http\Request;

class PresensiMasukController extends Controller
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
            'jam' => 'required|date_format:H:i',
            'latitude' => 'required|string',
            'longtitude' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $existingRecord = PresensiMasuk::where('shift_id', $request->shift_id)
        ->where('companies_users_id', $request->companies_users_id)
        ->where('tanggal', $request->tanggal)
        ->first();

        if ($existingRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.',
            ], 422);
        }


        $status = null;

        $jamMasuk = strtotime($request->jam);
        $jamBatasRecord = Jam::where('shift_id', $request->shift_id)->first();
        
        if ($jamBatasRecord) {
            $jamBatas = strtotime($jamBatasRecord->jam_masuk);
        
            if ($jamMasuk <= $jamBatas) {
                $status = 'Tepat Waktu';
            } else {
                $status = 'Terlambat';
            }
        } else {
            
            $status = 'Shift tidak ditemukan';
        }
        
        $presensiMasuk = PresensiMasuk::create([
            'shift_id' => $request->shift_id,
            'companies_users_id' => $request->companies_users_id,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude,
            'status' => $status,
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
        $presensiMasuk = PresensiMasuk::find($id);

        if (!$presensiMasuk) {
            return response()->json(['message' => 'Presensi Masuk tidak ditemukan.'], 404);
        }

        $presensiMasuk->delete();

        // Kembalikan respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Presensi Masuk berhasil dihapus!',
        ], 200);    
    }
    
}
