<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

   
}
