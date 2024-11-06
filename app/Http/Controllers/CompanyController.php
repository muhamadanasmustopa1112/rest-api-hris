<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyUser;
use App\Models\Perizinan;
use App\Models\Lembur;
use App\Models\Kasbon;
use App\Http\Resources\CompanyResource;

class CompanyController extends Controller
{
    //
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'company_name' => 'required|string',
            'user_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        // Buat perusahaan baru
        $company = Company::create(['name' => $request->company_name]);

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role admin ke pengguna
        $user->assignRole('admin');

        // Simpan relasi pengguna dengan perusahaan
        $user->companies()->attach($company->id, ['role_id' => 1]);

        return response()->json(['message' => 'Company and user registered successfully'], 201);
    }

    public function dashboard($id)
    {        
        $dataEmployee = CompanyUser::where('company_id', $id)->get();
        $dataPerizinan = Perizinan::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();
        $dataLembur = Lembur::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();
        $dataKasbon = Kasbon::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();

        //lembur
        $dataLemburOnProsses = Lembur::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'On Prosses')
        ->get();
        $dataLemburSuccess = Lembur::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'Success')
        ->get();
        $dataLemburDecline = Lembur::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'Decline')
        ->get();

        //kasbon
        $dataKasbonOnProsses = Kasbon::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'On Prosses')
        ->get();
        $dataKasbonSuccess = Kasbon::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'Success')
        ->get();
        $dataKasbonDecline = Kasbon::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'Decline')
        ->get();

        //perizinan
        $dataPerizinanOnProsses = Perizinan::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'On Prosses')
        ->get();
        $dataPerizinanSuccess = Perizinan::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'Success')
        ->get();
        $dataPerizinanDecline = Perizinan::whereHas('companyUser.company', function ($query) use ($id) {
            $query->where('id', $id);
        })
        ->where('status', 'Decline')
        ->get();

        $totalPerizinan = $dataPerizinan->count();
        $totalLembur = $dataLembur->count();
        $totalKasbon = $dataKasbon->count();

        $totalLemburOnProsses = $dataLemburOnProsses->count();
        $totalLemburSuccess = $dataLemburSuccess->count();
        $totalLemburDecline = $dataLemburDecline->count();

        $totalKasbonOnProsses = $dataKasbonOnProsses->count();
        $totalKasbonSuccess = $dataKasbonSuccess->count();
        $totalKasbonDecline = $dataKasbonDecline->count();

        $totalPerizinanOnProsses = $dataPerizinanOnProsses->count();
        $totalPerizinanSuccess = $dataPerizinanSuccess->count();
        $totalPerizinanDecline = $dataPerizinanDecline->count();

        return response()->json([
            'totals' => [
                'totalPerizinan' => $totalPerizinan,
                'totalLembur' => $totalLembur,
                'totalKasbon' => $totalKasbon,
            ],
            'lembur' => [
                'totalOnProsses' => $totalLemburOnProsses,
                'totalSuccess' => $totalLemburSuccess,
                'totalDecline' => $totalLemburDecline
            ], 
            'kasbon' => [
                'totalOnProsses' => $totalKasbonOnProsses,
                'totalSuccess' => $totalKasbonSuccess,
                'totalDecline' => $totalKasbonDecline
            ],
            'perizinan' => [
                'totalOnProsses' => $totalPerizinanOnProsses,
                'totalSuccess' => $totalPerizinanSuccess,
                'totalDecline' => $totalPerizinanDecline
            ],
        ], 200);
    }

    public function getDetailCompany($id)
    {
        $user = Company::with(['users' => function($query) use ($id) {
            $query->where('company_id', $id)
                  ->where('id', $id);
        }])->find($id);

        if (!$user) {
            return response()->json(['message' => 'Company User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Berhasil mengambil data'
        ], 200);  
    }

}
