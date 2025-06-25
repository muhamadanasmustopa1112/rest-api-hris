<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;

class PerjalananDinasController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::with(['company', 'employee']);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('employee_id')) {
            $query->where('companies_users_id', $request->employee_id);
        }

        $results = $query->get();

        return response()->json([
            'data' => $results
        ]);
    }

    public function show($id)
    {
        return PerjalananDinas::with(['company', 'employee'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'companies_users_id' => 'required|exists:companies_users,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'required|date|after_or_equal:tanggal_berangkat',
            'tujuan' => 'required|string',
            'keperluan' => 'required|string',
        ]);

        $validated['status'] = 'pending';

        $data = PerjalananDinas::create($validated);

        return response()->json([
            'message' => 'Perjalanan dinas berhasil dibuat',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $dinas = PerjalananDinas::findOrFail($id);

        $validated = $request->validate([
            'tanggal_berangkat' => 'sometimes|date',
            'tanggal_pulang' => 'sometimes|date|after_or_equal:tanggal_berangkat',
            'tujuan' => 'sometimes|string',
            'keperluan' => 'sometimes|string',
            'status' => 'in:pending,approved,rejected',
            'rejected_reason' => 'nullable|string',
        ]);

        $dinas->update($validated);

        return response()->json([
            'message' => 'Perjalanan dinas berhasil diperbarui',
            'data' => $dinas
        ]);
    }

    public function destroy($id)
    {
        $dinas = PerjalananDinas::findOrFail($id);
        $dinas->delete();

        return response()->json([
            'message' => 'Perjalanan dinas berhasil dihapus'
        ]);
    }
}
