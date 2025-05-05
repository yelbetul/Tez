<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ProvinceCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProvinceCodeController extends Controller
{
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'province_code' => 'required|string|size:2|unique:province_codes,province_code',
                'province_name' => 'required|string|max:15',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'province_code' => 'sometimes|string|size:2|unique:province_codes,province_code,' . $request->id,
                'province_name' => 'sometimes|string|max:15',
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

    public function index()
    {
        $provinceCodes = ProvinceCode::all();

        if ($provinceCodes->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Şehir kaydı bulunamadı.']);
        }

        return response()->json(['success' => true, 'data' => $provinceCodes]);
    }
    
    public function indexUser()
    {
       return DB::table('province_codes')
        ->select(
            'province_code',
            'province_name'
        )
        ->orderBy('province_code')
        ->get()
        ->map(function ($item) {
            return [
                'province_code' => $item->province_code,
                'province_name' => $item->province_name,
            ];
        });
    }
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $provinceCode = new ProvinceCode();
        $provinceCode->province_code = $request->province_code;
        $provinceCode->province_name = $request->province_name;

        if ($provinceCode->save()) {
            return response()->json(['success' => true, 'message' => 'Şehir başarıyla oluşturuldu.', 'data' => $provinceCode]);
        }

        return response()->json(['success' => false, 'message' => 'Şehir oluşturulamadı.']);
    }


    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $provinceCode = ProvinceCode::find($id);

        if (!$provinceCode) {
            return response()->json(['success' => false, 'message' => 'Şehir bulunamadı.']);
        }

        $provinceCode->province_code = $request->province_code ?? $provinceCode->province_code;
        $provinceCode->province_name = $request->province_name ?? $provinceCode->province_name;

        if ($provinceCode->save()) {
            return response()->json(['success' => true, 'message' => 'Şehir başarıyla güncellendi.', 'data' => $provinceCode]);
        }

        return response()->json(['success' => false, 'message' => 'Şehir güncellenemedi.']);
    }

    public function destroy(Request $request, $id)
    {

        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $provinceCode = ProvinceCode::find($id);

        if (!$provinceCode) {
            return response()->json(['success' => false, 'message' => 'Şehir bulunamadı.']);
        }

        if ($provinceCode->delete()) {
            return response()->json(['success' => true, 'message' => 'Şehir başarıyla silindi.']);
        }

        return response()->json(['success' => false, 'message' => 'Şehir silinemedi.']);
    }
}
