<?php

namespace App\Http\Controllers;

use App\Models\InjuryCause;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class InjuryCauseController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'injury_cause_code' => 'required|string|max:16|unique:injury_causes,injury_cause_code',
                'group_code'        => 'required|string|max:3',
                'group_name'        => 'required|string|max:255',
                'sub_group_code'    => 'required|string|max:3',
                'sub_group_name'    => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'injury_cause_code' => 'sometimes|string|max:16|unique:injury_causes,injury_cause_code,' . $request->id,
                'group_code'        => 'sometimes|string|max:3',
                'group_name'        => 'sometimes|string|max:255',
                'sub_group_code'    => 'sometimes|string|max:3',
                'sub_group_name'    => 'sometimes|string|max:255',
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
        $injuryCauses = InjuryCause::all();

        if ($injuryCauses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $injuryCauses
        ]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $injuryCause = new InjuryCause();
        $injuryCause->injury_cause_code = $request->injury_cause_code;
        $injuryCause->group_code        = $request->group_code;
        $injuryCause->group_name        = $request->group_name;
        $injuryCause->sub_group_code    = $request->sub_group_code;
        $injuryCause->sub_group_name    = $request->sub_group_name;

        if ($injuryCause->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Kayıt başarıyla oluşturuldu.',
                'data' => $injuryCause
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kayıt oluşturulurken hata oluştu.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $injuryCause = InjuryCause::find($id);
        if (!$injuryCause) {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $injuryCause->injury_cause_code = $request->injury_cause_code ?? $injuryCause->injury_cause_code;
        $injuryCause->group_code        = $request->group_code ?? $injuryCause->group_code;
        $injuryCause->group_name        = $request->group_name ?? $injuryCause->group_name;
        $injuryCause->sub_group_code    = $request->sub_group_code ?? $injuryCause->sub_group_code;
        $injuryCause->sub_group_name    = $request->sub_group_name ?? $injuryCause->sub_group_name;

        if ($injuryCause->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Kayıt başarıyla güncellendi.',
                'data' => $injuryCause
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kayıt güncellenirken hata oluştu.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $injuryCause = InjuryCause::find($id);
        if (!$injuryCause) {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt bulunamadı!'
            ]);
        }

        if ($injuryCause->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Kayıt başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kayıt silinirken hata oluştu.'
        ]);
    }
}
