<?php

namespace App\Http\Controllers;
use App\Models\Division;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $company_id = $request->input('company_id');
            
            $data = Division::where('company_id', $company_id)->get();
    
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Division not found',
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
        //

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'company_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $division = Division::create([
            'name'=> $request->name,
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
        
        $division = Division::find($id);

        if($division == null){
            return response()->json([
                'status' => 'error',
                'data' => $division,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' => $division
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
    
            $division = Division::findOrFail($id);
    
            $division->name = $request->input('name');
            $division->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Divisi berhasil diupdate',
                'data' => $division
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

   
    public function destroy(string $id)
    {
      
        $division = Division::find($id);

        // Cek apakah divisi ditemukan
        if (!$division) {
            return response()->json(['message' => 'Divisi tidak ditemukan.'], 404);
        }

        // Hapus divisi
        $division->delete();

        // Kembalikan respons sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Divisi berhasil dihapus!',
        ], 200);    
    }
}
