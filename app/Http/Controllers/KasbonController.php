<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kasbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\KasbonResource;

class KasbonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company_id = $request->input('company_id');
        
        $data = Kasbon::whereHas('companyUser.company', function ($query) use ($company_id) {
            $query->where('id', $company_id);
        })->get();

        if($data->isEmpty()){
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
                'data' => KasbonResource::collection($data),
            ], 200);
        }else {
            return response()->json([
                'status' => 'success',
                'data' => KasbonResource::collection($data),
            ], 200);
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
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|integer',
            'tenor' => 'required|integer',
            'keterangan' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $kasbon = Kasbon::create([
            'companies_users_id' => $request->companies_users_id,
            'tanggal' => $request->tanggal,
            'nominal' => $request->nominal,
            'tenor' => $request->tenor,
            'keterangan' => $request->keterangan,
            'status' => $request->status,       
        ]);

        if($kasbon){
            return response()->json([
                'status' => true,
                'message' => 'success',
            ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'error created',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kasbon = Kasbon::find($id);

        if($kasbon == null){
            return response()->json([
                'status' => 'error',
                'data' => $kasbon,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' =>  new KasbonResource($kasbon),
                'message' => 'Success get data kasbon'
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
            'nominal' => 'required|integer',
            'tenor' => 'required|integer',
            'keterangan' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        $kasbon = Kasbon::find($id);

        if (!$kasbon) {
            return response()->json(['message' => 'Kasbon User not found'], 404);
        }

        $kasbon->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data Kasbon berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $kasbon = Kasbon::findOrFail($id); 
            $kasbon->delete(); 
            
            return response()->json([
                'status' => true,
                'message' => 'kasbon deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'kasbon not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete kasbon',
            ], 500);
        }
    }
}
