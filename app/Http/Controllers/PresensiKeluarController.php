<?php

namespace App\Http\Controllers;
use App\Models\PresensiKeluar;
use App\Models\Jam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;

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
            'status' => 'nullable|string',
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
            'status' =>  $request->status ?? '-',
            'keteragan' =>  $request->keterangan ?? '-',
        ]);

        if ($presensiKeluar) {
            $user = User::where('companies_users_id', $request->companies_users_id)->first();

            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Clock-Out Successful',
                    'body' => 'You have successfully clocked out on ' . Carbon::parse($request->tanggal)->format('D, d F Y') . ' at ' . Carbon::createFromFormat('H:i', $request->jam)->format('h:i A'),
                    'type' => 'attendance',
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Presensi berhasil dibuat.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat presensi.',
            ], 500);
        }
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
