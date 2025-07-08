<?php

namespace App\Http\Controllers;
use App\Models\PresensiMasuk;
use App\Models\Jam;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
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
            'jam' => 'required',
            'latitude' => 'required|string',
            'longtitude' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);   
        }

        $existingRecord = PresensiMasuk::where('shift_id', $request->shift_id)
        ->where('companies_users_id', $request->companies_users_id)
        ->where('tanggal', $request->tanggal)
        ->first();

        if ($existingRecord) {
            return response()->json([
                'status' => false,
                'errors' => 'Anda sudah melakukan absensi hari ini.',
            ], 422);
        }


        $status = null;

        $jamMasuk = strtotime($request->jam);
        $jamBatasRecord = Jam::where('shift_id', $request->shift_id)->first();
        
        if ($jamBatasRecord) {
            $jamBatas = strtotime($jamBatasRecord->jam_masuk);
        
            if ($jamMasuk <= $jamBatas) {
                $status = 'On Time';
            } else {
                $status = 'Late';
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

        if ($presensiMasuk) {
            $user = User::where('companies_users_id', $request->companies_users_id)->first();

            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Clock-In Successful',
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
