<?php

namespace App\Http\Controllers;

use App\Models\SpecialActivitiesBeforeAccident;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class SpecialActivitiesBeforeAccidentController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'special_activity_code' => 'required|string|max:16|unique:special_activities_before_accidents,special_activity_code',
                'group_code'            => 'required|string|max:3',
                'group_name'            => 'required|string|max:255',
                'sub_group_code'        => 'required|string|max:3',
                'sub_group_name'        => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'special_activity_code' => 'sometimes|string|max:16|unique:special_activities_before_accidents,special_activity_code,' . $request->id,
                'group_code'            => 'sometimes|string|max:3',
                'group_name'            => 'sometimes|string|max:255',
                'sub_group_code'        => 'sometimes|string|max:3',
                'sub_group_name'        => 'sometimes|string|max:255',
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
        $specialActivities = SpecialActivitiesBeforeAccident::all();

        if ($specialActivities->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        return response()->json(['success' => true, 'data' => $specialActivities]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $specialActivity = new SpecialActivitiesBeforeAccident();
        $specialActivity->special_activity_code = $request->special_activity_code;
        $specialActivity->group_code            = $request->group_code;
        $specialActivity->group_name            = $request->group_name;
        $specialActivity->sub_group_code        = $request->sub_group_code;
        $specialActivity->sub_group_name        = $request->sub_group_name;

        if ($specialActivity->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Özel aktivite kaydı başarıyla oluşturuldu.',
                'data'    => $specialActivity
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Özel aktivite kaydı kaydedilirken hata oluştu.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $specialActivity = SpecialActivitiesBeforeAccident::find($id);
        if (!$specialActivity) {
            return response()->json(['success' => false, 'message' => 'Özel aktivite kaydı bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $specialActivity->special_activity_code = $request->special_activity_code ?? $specialActivity->special_activity_code;
        $specialActivity->group_code            = $request->group_code ?? $specialActivity->group_code;
        $specialActivity->group_name            = $request->group_name ?? $specialActivity->group_name;
        $specialActivity->sub_group_code        = $request->sub_group_code ?? $specialActivity->sub_group_code;
        $specialActivity->sub_group_name        = $request->sub_group_name ?? $specialActivity->sub_group_name;

        if ($specialActivity->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Özel aktivite kaydı başarıyla güncellendi.',
                'data'    => $specialActivity
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Özel aktivite kaydı güncellenirken hata oluştu.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $specialActivity = SpecialActivitiesBeforeAccident::find($id);
        if (!$specialActivity) {
            return response()->json(['success' => false, 'message' => 'Özel aktivite kaydı bulunamadı!']);
        }

        if ($specialActivity->delete()) {
            return response()->json(['success' => true, 'message' => 'Özel aktivite kaydı başarıyla silindi.']);
        }

        return response()->json(['success' => false, 'message' => 'Özel aktivite kaydı silinirken hata oluştu.']);
    }
}
