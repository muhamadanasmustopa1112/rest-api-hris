<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\CompanyUser;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CompanyUserResource;
use App\Models\Jabatan; 
use Spatie\Permission\Models\Role;
use App\Models\DivisionPosition;

class CompanyUserController extends Controller
{
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'nullable|string|max:16|unique:companies_users,nik',
            'no_kk' => 'nullable|string|max:16',
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|string|email|unique:companies_users,email',
            'no_hp_darurat' => 'required|string',
            'status' => 'required|string',
            'alamat' => 'required|string',
            'bpjs_kesehatan' => 'nullable|string',
            'bpjs_ketenagakerjaan' => 'nullable|string',
            'npwp' => 'nullable|string',
            'division_id' => 'required|integer',
            'jabatan_id' => 'required|integer',
            'company_id' => 'required|integer',
            'foto_karyawan' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'ktp_karyawan' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'ijazah_karyawan' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        $fotoKaryawan = $request->file('foto_karyawan') 
            ? $request->file('foto_karyawan')->store('uploads/foto_karyawan', 'public') 
            : null;

        $ktpKaryawan = $request->file('ktp_karyawan') 
            ? $request->file('ktp_karyawan')->store('uploads/ktp_karyawan', 'public') 
            : null;

        $ijazahKaryawan = $request->file('ijazah_karyawan') 
            ? $request->file('ijazah_karyawan')->store('uploads/ijazah_karyawan', 'public') 
            : null;


        DivisionPosition::ensureExists($request->division_id, $request->jabatan_id);


        $companyUser = CompanyUser::create([
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'name' => $request->name,
            'gender' => $request->gender,
            'tgl_lahir' => $request->tgl_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'no_hp_darurat' => $request->no_hp_darurat,
            'status' => $request->status,
            'alamat' => $request->alamat,
            'bpjs_kesehatan' => $request->bpjs_kesehatan,
            'bpjs_ketenagakerjaan' => $request->bpjs_ketenagakerjaan,
            'npwp' => $request->npwp,
            'division_id' => $request->division_id,
            'jabatan_id' => $request->jabatan_id,
            'foto_karyawan' => $fotoKaryawan,
            'ktp_karyawan' => $ktpKaryawan,
            'ijazah_karyawan' => $ijazahKaryawan,
            'company_id' => $request->company_id,
            'qr_code' => null, 
        ]);


        $qrCode = 'qr_codes/' . uniqid() . '.png';
        QrCode::format('png')->size(200)->generate($companyUser->id, public_path('storage/' . $qrCode));

        $companyUser->update([
            'qr_code' => $qrCode
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(123456),
            'company_id' => $request->company_id,
            'companies_users_id' => $companyUser->id
        ]);
        
        $jabatan = Jabatan::find($request->jabatan_id);

        if ($jabatan) {
            Role::firstOrCreate([
                'name' => $jabatan->name,
                'guard_name' => 'web',
            ]);

            $user->assignRole($jabatan->name);
        } else {
            $user->assignRole('employee');
        }

        return response()->json([
            'success' => true,
            'message' => 'User added successfully',
            'user_account' => $user,
            'qr_code_url' => asset('storage/' . $qrCode)
        ], 201);
    }

    public function show($id)
    {
        $user = CompanyUser::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'Company User not found'], 404);
        }

        return new CompanyUserResource($user);    
    }

    public function update(Request $request, $id)
    {
        
        $validator = Validator::make($request->all(), [
            'nik' => 'nullable|string|max:16|unique:companies_users,nik,' . $id,
            'no_kk' => 'nullable|string|max:16',
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'no_hp' => 'required|string',
            'email' => 'required|string|email|unique:companies_users,email,' . $id,
            'no_hp_darurat' => 'required|string',
            'status' => 'required|string',
            'alamat' => 'required|string',
            'bpjs_kesehatan' => 'nullable|string',
            'bpjs_ketenagakerjaan' => 'nullable|string',
            'npwp' => 'nullable|string',
            'division_id' => 'required|integer',
            'jabatan_id' => 'required|integer',
            'foto_karyawan' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'ktp_karyawan' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'ijazah_karyawan' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);      
        }

        
        $employee = CompanyUser::findOrFail($id);

       
        $employee->update($validator->validated());

        
        if ($request->hasFile('foto_karyawan')) {
            $fileName = time() . '_' . $request->file('foto_karyawan')->getClientOriginalName();
            $filePath = $request->file('foto_karyawan')->storeAs('uploads/foto_karyawan', $fileName, 'public');
            $employee->foto_karyawan = $filePath;
        }

        if ($request->hasFile('ktp_karyawan')) {
            $fileName = time() . '_' . $request->file('ktp_karyawan')->getClientOriginalName();
            $filePath = $request->file('ktp_karyawan')->storeAs('uploads/ktp_karyawan', $fileName, 'public');
            $employee->ktp_karyawan = $filePath;
        }

        if ($request->hasFile('ijazah_karyawan')) {
            $fileName = time() . '_' . $request->file('ijazah_karyawan')->getClientOriginalName();
            $filePath = $request->file('ijazah_karyawan')->storeAs('uploads/ijazah_karyawan', $fileName, 'public');
            $employee->ijazah_karyawan = $filePath;
        }

        
        $employee->save();

        
        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diupdate',
            'data' => $employee,
        ], 200);
    }

    public function getCompanyUserWhereCompany($id_company)
    {

         $data = CompanyUser::where('company_id', $id_company)->get();
         return CompanyUserResource::collection($data);
    }

    

    public function sendEmail(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
            'subject' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
            'subject' => $request->input('subject'),
        ];

        try {
            Mail::to('muhamadanasmustopa1112@gmail.com')->send(new OrderShipped($data)); 

            return response()->json(['message' => 'Email sent successfully!']);

        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            Log::error('Failed to send email to: ' . $data['email']);
            return response()->json(['message' => 'Failed to send email.'], 500);
        }
    }

    public function deleteEmployee($id)
    {
        try {
            $employee = CompanyUser::findOrFail($id); 
            $employee->delete(); 
            
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee',
            ], 500);
        }
    }
}
