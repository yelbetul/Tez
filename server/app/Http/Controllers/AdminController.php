<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;

class AdminController extends Controller
{
    private function validateRequest($request, $type = 'store', $id = null)
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'name_surname' => 'required|string|max:255',
                'username' => 'required|string|max:50|unique:admins',
                'password' => 'required|string|min:6',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'name_surname' => 'sometimes|string|max:255',
                'username' => 'sometimes|string|max:50|unique:admins,username,' . $id,
                'password' => 'sometimes|string|min:6',
            ];
        }
        elseif ($type === 'login') {
            $rules = [
                'username' => 'required|string|max:50',
                'password' => 'required|string|min:6',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }

    private function authenticate(Request $request)
    {
        $admin_id = $request->header('X-ADMIN-ID');
        $api_key = $request->header('X-API-KEY');
        $secret_key = $request->header('X-SECRET-KEY');

        if (!$admin_id || !$api_key || !$secret_key) {
            return response()->json(['success' => false, 'message' => 'Kimlik doğrulama bilgileri eksik!']);
        }

        $admin = Admin::where('id', $admin_id)
                      ->where('api_key', $api_key)
                      ->where('secret_key', $secret_key)
                      ->first();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Yetkiniz bulunmamaktadır!']);
        }

        return $admin;
    }
    public function login(Request $request)
    {
        $validation = $this->validateRequest($request, 'login');
        if ($validation) return $validation;

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !password_verify($request->password, $admin->password)) {
            return response()->json(['success' => false, 'message' => 'Kullanıcı adı veya şifre hatalı!']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Giriş başarılı!',
            'admin' => $admin
        ]);
    }

    public function index(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $admins = Admin::all();
        return response()->json(['success' => true, 'admins' => $admins]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $admin = Admin::create($request->all());

        return response()->json([
            'message' => 'Admin başarıyla oluşturuldu.',
            'admin' => $admin
        ]);
    }

    public function show(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Admin bulunamadı.']);
        }
        return response()->json($admin);
    }
    
    public function searchAdmin(Request $request, $id)
    {
        $api_key = $request->header('X-API-KEY');
        $secret_key = $request->header('X-SECRET-KEY');
        
        if($api_key === "fKenAx;NuzBMj#[q'P|N" && $secret_key === "3Lsw,~yd*hc5sLugNETY"){
            return response()->json(['success' => false, 'message' => 'Kimlik doğrulama bilgileri eksik!']);
        }

        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Admin bulunamadı.']);
        }
        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'update', $id);
        if ($validation) return $validation;

        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Admin bulunamadı.']);
        }

        $admin->update($request->all());

        return response()->json([
            'message' => 'Admin başarıyla güncellendi.',
            'admin' => $admin
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Admin bulunamadı.']);
        }

        $admin->delete();

        return response()->json(['message' => 'Admin başarıyla silindi.']);
    }
}
