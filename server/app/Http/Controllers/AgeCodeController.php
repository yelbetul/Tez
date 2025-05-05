<?php

namespace App\Http\Controllers;

use App\Models\AgeCode;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AgeCodeController extends Controller
{
    // Validasyon fonksiyonu
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'age' => 'required|integer|min:1|max:150|unique:age_codes,age',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'age' => 'sometimes|integer|min:1|max:150|unique:age_codes,age,' . $request->id,
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        return null;
    }

    // Kimlik doğrulama fonksiyonu
    private function authenticate(Request $request)
    {
        $admin_id = $request->header('X-ADMIN-ID');
        $api_key = $request->header('X-API-KEY');
        $secret_key = $request->header('X-SECRET-KEY');

        if (!$admin_id || !$api_key || !$secret_key) {
            return response()->json([
                'success' => false,
                'message' => 'Kimlik doğrulama bilgileri eksik!'
            ]);
        }

        $admin = Admin::where('id', $admin_id)
                      ->where('api_key', $api_key)
                      ->where('secret_key', $secret_key)
                      ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkiniz bulunmamaktadır!'
            ]);
        }

        return $admin;
    }

    /**
     * Listeleme
     */
    public function index()
    {
        $ageCodes = AgeCode::all();

        if ($ageCodes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Yaş kodu kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $ageCodes
        ]);
    }

    public function indexUser()
    {
       return DB::table('province_codes')
        ->select(
            'age'
        )
        ->orderBy('age')
        ->get()
        ->map(function ($item) {
            return [
                'age' => $item->age,
            ];
        });
    }

    /**
     * Oluşturma
     */
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $ageCode = new AgeCode();
        $ageCode->age = $request->age;

        if ($ageCode->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Yaş kodu başarıyla oluşturuldu.',
                'data' => $ageCode
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yaş kodu oluşturulurken bir hata oluştu.'
        ]);
    }

    /**
     * Güncelleme
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $ageCode = AgeCode::find($id);
        if (!$ageCode) {
            return response()->json([
                'success' => false,
                'message' => 'Yaş kodu bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $ageCode->age = $request->age ?? $ageCode->age;

        if ($ageCode->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Yaş kodu başarıyla güncellendi.',
                'data' => $ageCode
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yaş kodu güncellenirken bir hata oluştu.'
        ]);
    }

    /**
     * Silme
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $ageCode = AgeCode::find($id);
        if (!$ageCode) {
            return response()->json([
                'success' => false,
                'message' => 'Yaş kodu bulunamadı!'
            ]);
        }

        if ($ageCode->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Yaş kodu başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yaş kodu silinirken bir hata oluştu.'
        ]);
    }
}
