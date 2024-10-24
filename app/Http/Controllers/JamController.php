<?php

namespace App\Http\Controllers;
use App\Models\Jam;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\JamResource;

use Illuminate\Http\Request;

class JamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company_id = $request->input('company_id');
        
        $data = Jam::whereHas('shift.company', function ($query) use ($company_id) {
            $query->where('id', $company_id);
        })->get();

        if($data->isEmpty()){
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
                'data' => JamResource::collection($data),
            ], 200);
        }else {
            return response()->json([
                'status' => 'success',
                'data' => JamResource::collection($data),
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
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:shift,id',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $jam = Jam::create([
            'shift_id' => $request->shift_id,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
       
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
        $jam = Jam::find($id);

        if($jam == null){
            return response()->json([
                'status' => 'error',
                'data' => $jam,
                'message' => 'Data Not Found'
            ], 404);
        }else {
            return response()->json([
                'status' => 'success',
                'data' =>  new JamResource($jam),
                'message' => 'Success get data jam'
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
            'shift_id' => 'required|exists:shift,id',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        $jam = Jam::find($id);

        if (!$jam) {
            return response()->json(['message' => 'Jam Kerja not found'], 404);
        }

        $jam->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data Jam berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $jam = Jam::findOrFail($id); 
            $jam->delete(); 
            
            return response()->json([
                'status' => true,
                'message' => 'jam deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'jam not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete jam',
            ], 500);
        }
    }
}
