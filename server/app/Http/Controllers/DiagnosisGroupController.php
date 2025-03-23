<?php

namespace App\Http\Controllers;

use App\Models\DiagnosisGroup;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class DiagnosisGroupController extends Controller
{
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'diagnosis_code'   => 'required|string|max:255|unique:diagnosis_groups,diagnosis_code',
                'group_code'       => 'required|string|max:3',
                'group_name'       => 'required|string|max:255',
                'sub_group_code'   => 'required|string|max:6',
                'sub_group_name'   => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'diagnosis_code'   => 'sometimes|string|max:255|unique:diagnosis_groups,diagnosis_code,' . $request->id,
                'group_code'       => 'sometimes|string|max:3',
                'group_name'       => 'sometimes|string|max:255',
                'sub_group_code'   => 'sometimes|string|max:6',
                'sub_group_name'   => 'sometimes|string|max:255',
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
        $diagnosisGroups = DiagnosisGroup::all();

        if ($diagnosisGroups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tanı grubu kaydı bulunamadı.']);
        }

        return response()->json(['success' => true, 'data' => $diagnosisGroups]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $diagnosisGroup = new DiagnosisGroup();
        $diagnosisGroup->diagnosis_code  = $request->diagnosis_code;
        $diagnosisGroup->group_code      = $request->group_code;
        $diagnosisGroup->group_name      = $request->group_name;
        $diagnosisGroup->sub_group_code  = $request->sub_group_code;
        $diagnosisGroup->sub_group_name  = $request->sub_group_name;

        if ($diagnosisGroup->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Tanı grubu başarıyla oluşturuldu.',
                'data'    => $diagnosisGroup
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tanı grubu oluşturulurken bir hata oluştu.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $diagnosisGroup = DiagnosisGroup::find($id);
        if (!$diagnosisGroup) {
            return response()->json(['success' => false, 'message' => 'Tanı grubu bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $diagnosisGroup->diagnosis_code  = $request->diagnosis_code ?? $diagnosisGroup->diagnosis_code;
        $diagnosisGroup->group_code      = $request->group_code ?? $diagnosisGroup->group_code;
        $diagnosisGroup->group_name      = $request->group_name ?? $diagnosisGroup->group_name;
        $diagnosisGroup->sub_group_code  = $request->sub_group_code ?? $diagnosisGroup->sub_group_code;
        $diagnosisGroup->sub_group_name  = $request->sub_group_name ?? $diagnosisGroup->sub_group_name;

        if ($diagnosisGroup->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Tanı grubu başarıyla güncellendi.',
                'data'    => $diagnosisGroup
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tanı grubu güncellenirken bir hata oluştu.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $diagnosisGroup = DiagnosisGroup::find($id);
        if (!$diagnosisGroup) {
            return response()->json(['success' => false, 'message' => 'Tanı grubu bulunamadı!']);
        }

        if ($diagnosisGroup->delete()) {
            return response()->json(['success' => true, 'message' => 'Tanı grubu başarıyla silindi.']);
        }

        return response()->json(['success' => false, 'message' => 'Tanı grubu silinirken bir hata oluştu.']);
    }
}
