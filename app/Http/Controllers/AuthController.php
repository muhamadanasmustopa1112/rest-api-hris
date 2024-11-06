<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function createCompanyAndUser(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'user_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $company = Company::create(['name' => $request->company_name]);
        $user = User::create([
            'name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'companies_users_id' => null
        ]);
        
        $user->assignRole('admin');

        return response()->json(['message' => 'Company and user created successfully'], 201);
    }


    public function login(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        if (Auth::attempt($request->only('email', 'password'))) {
          
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            $roles = $user->getRoleNames(); 

            return response()->json([
                'message' => 'success',
                'user' => $user,
                'token' => $token,
                'role' => $roles
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function changePassword(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mencari pengguna berdasarkan ID
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        // Memeriksa apakah password lama benar
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak cocok'
            ], 400);
        }

        // Mengubah password pengguna
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diperbarui'
        ], 200);
    }
}
