<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Validator;

class JabatanController extends Controller
{
 
    public function index(Request $request)
    {
        
        try {

            $company_id = $request->input('company_id');
            
            $data = Jabatan::where('company_id', $company_id)->get();
    
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan not found',
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
            'company_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $jabatan = Jabatan::create([
            'name'=> $request->name,
            'company_id'=> $request->company_id,
        ]);

        return response()->json(['message' => 'success']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jabatan = Jabatan::find($id);

        if(!$jabatan){
            return response()->json([
                'status' => 'error',
                'data' => $jabatan,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' => $jabatan,
                'message' => 'Success get data jabatan'
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
            ]);
    
            $jabatan = Jabatan::findOrFail($id);
    
            $jabatan->name = $request->input('name');
            $jabatan->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Divisi berhasil diupdate',
                'data' => $jabatan
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
        $jabatan = Jabatan::find($id);

        // Cek apakah divisi ditemukan
        if (!$jabatan) {
            return response()->json(['message' => 'Divisi tidak ditemukan.'], 404);
        }

        // Hapus divisi
        $jabatan->delete();

        // Kembalikan respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Divisi berhasil dihapus!',
        ], 200); 
    }
}
