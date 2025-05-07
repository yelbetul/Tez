<?php

namespace App\Http\Controllers;

use App\Models\InjuryType;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InjuryTypeController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'injury_code'      => 'required|string|max:255|unique:injury_types,injury_code',
                'group_code'       => 'required|string|max:255',
                'group_name'       => 'required|string|max:255',
                'sub_group_code'   => 'required|string|max:255',
                'sub_group_name'   => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'injury_code'      => 'sometimes|string|max:255|unique:injury_types,injury_code,' . $request->id,
                'group_code'       => 'sometimes|string|max:255',
                'group_name'       => 'sometimes|string|max:255',
                'sub_group_code'   => 'sometimes|string|max:255',
                'sub_group_name'   => 'sometimes|string|max:255',
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

    /**
     * Kimlik doğrulama fonksiyonu
     */
    private function authenticate(Request $request)
    {
        $admin_id   = $request->header('X-ADMIN-ID');
        $api_key    = $request->header('X-API-KEY');
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

    public function index()
    {
        $injuryTypes = InjuryType::all();

        if ($injuryTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Yara türü kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $injuryTypes
        ]);
    }

    public function indexUser()
    {
        return DB::table('injury_types')
            ->select(
                'injury_code',
                'group_code',
                'group_name',
                'sub_group_code',
                'sub_group_name'
            )
            ->orderBy('group_code')
            ->orderBy('sub_group_code')
            ->orderBy('injury_code')
            ->get()
            ->map(function ($item) {
                return [
                    'injury_code' => $item->injury_code,
                    'group_code' => $item->group_code,
                    'group_name' => $item->group_name,
                    'sub_group_code' => $item->sub_group_code,
                    'sub_group_name' => $item->sub_group_name
                ];
            });
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $injuryType = new InjuryType();
        $injuryType->injury_code      = $request->injury_code;
        $injuryType->group_code       = $request->group_code;
        $injuryType->group_name       = $request->group_name;
        $injuryType->sub_group_code   = $request->sub_group_code;
        $injuryType->sub_group_name   = $request->sub_group_name;

        if ($injuryType->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Yara Türü başarıyla kaydedildi.',
                'data' => $injuryType
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yara Türü kaydedilirken hata oluştu.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $injuryType = InjuryType::find($id);
        if (!$injuryType) {
            return response()->json([
                'success' => false,
                'message' => 'Yara Türü bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $injuryType->injury_code      = $request->injury_code ?? $injuryType->injury_code;
        $injuryType->group_code       = $request->group_code ?? $injuryType->group_code;
        $injuryType->group_name       = $request->group_name ?? $injuryType->group_name;
        $injuryType->sub_group_code   = $request->sub_group_code ?? $injuryType->sub_group_code;
        $injuryType->sub_group_name   = $request->sub_group_name ?? $injuryType->sub_group_name;

        if ($injuryType->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Yara Türü başarıyla güncellendi.',
                'data' => $injuryType
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yara Türü güncellenirken hata oluştu.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $injuryType = InjuryType::find($id);
        if (!$injuryType) {
            return response()->json([
                'success' => false,
                'message' => 'Yara Türü bulunamadı!'
            ]);
        }

        if ($injuryType->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Yara Türü başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Yara Türü silinirken hata oluştu.'
        ]);
    }
}
