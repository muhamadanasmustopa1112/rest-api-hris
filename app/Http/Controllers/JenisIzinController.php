<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisIzin;
use Illuminate\Support\Facades\Validator;

class JenisIzinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        try {

            $data = JenisIzin::all();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis Izin not found',
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
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan user karyawan ke database
        $division = JenisIzin::create([
            'name'=> $request->name
        ]);

        return response()->json(['message' => 'success']);
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
        //
    }
}
