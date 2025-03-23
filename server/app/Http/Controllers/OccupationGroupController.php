<?php

namespace App\Http\Controllers;

use App\Models\OccupationGroup;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class OccupationGroupController extends Controller
{
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'code'             => 'required|string|max:16|unique:occupation_groups,code',
                'occupation_code'  => 'required|string|max:16',
                'occupation_name'  => 'required|string|max:16',
                'group_code'       => 'required|string|max:3',
                'group_name'       => 'required|string|max:255',
                'sub_group_code'   => 'required|string|max:3',
                'sub_group_name'   => 'required|string|max:255',
                'pure_code'        => 'required|string|max:3',
                'pure_name'        => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code'             => 'sometimes|string|max:16|unique:occupation_groups,code,' . $request->id,
                'occupation_code'  => 'sometimes|string|max:16',
                'occupation_name'  => 'sometimes|string|max:16',
                'group_code'       => 'sometimes|string|max:3',
                'group_name'       => 'sometimes|string|max:255',
                'sub_group_code'   => 'sometimes|string|max:3',
                'sub_group_name'   => 'sometimes|string|max:255',
                'pure_code'        => 'sometimes|string|max:3',
                'pure_name'        => 'sometimes|string|max:255',
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
        $occupationGroups = OccupationGroup::all();

        if ($occupationGroups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu kaydı bulunamadı.']);
        }

        return response()->json(['success' => true, 'data' => $occupationGroups]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $occupationGroup = new OccupationGroup();
        $occupationGroup->code             = $request->code;
        $occupationGroup->occupation_code  = $request->occupation_code;
        $occupationGroup->occupation_name  = $request->occupation_name;
        $occupationGroup->group_code       = $request->group_code;
        $occupationGroup->group_name       = $request->group_name;
        $occupationGroup->sub_group_code   = $request->sub_group_code;
        $occupationGroup->sub_group_name   = $request->sub_group_name;
        $occupationGroup->pure_code        = $request->pure_code;
        $occupationGroup->pure_name        = $request->pure_name;

        if ($occupationGroup->save()) {
            return response()->json(['success' => true, 'message' => 'Meslek Grubu başarıyla kaydedildi.', 'data' => $occupationGroup]);
        }

        return response()->json(['success' => false, 'message' => 'Meslek Grubu kaydedilirken hata oluştu.']);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $occupationGroup = OccupationGroup::find($id);
        if (!$occupationGroup) {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $occupationGroup->code             = $request->code ?? $occupationGroup->code;
        $occupationGroup->occupation_code  = $request->occupation_code ?? $occupationGroup->occupation_code;
        $occupationGroup->occupation_name  = $request->occupation_name ?? $occupationGroup->occupation_name;
        $occupationGroup->group_code       = $request->group_code ?? $occupationGroup->group_code;
        $occupationGroup->group_name       = $request->group_name ?? $occupationGroup->group_name;
        $occupationGroup->sub_group_code   = $request->sub_group_code ?? $occupationGroup->sub_group_code;
        $occupationGroup->sub_group_name   = $request->sub_group_name ?? $occupationGroup->sub_group_name;
        $occupationGroup->pure_code        = $request->pure_code ?? $occupationGroup->pure_code;
        $occupationGroup->pure_name        = $request->pure_name ?? $occupationGroup->pure_name;

        if ($occupationGroup->save()) {
            return response()->json(['success' => true, 'message' => 'Meslek Grubu başarıyla güncellendi.', 'data' => $occupationGroup]);
        }

        return response()->json(['success' => false, 'message' => 'Meslek Grubu güncellenirken hata oluştu.']);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $occupationGroup = OccupationGroup::find($id);
        if (!$occupationGroup) {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu bulunamadı!']);
        }

        if ($occupationGroup->delete()) {
            return response()->json(['success' => true, 'message' => 'Meslek Grubu başarıyla silindi.']);
        }

        return response()->json(['success' => false, 'message' => 'Meslek Grubu silinirken hata oluştu.']);
    }
}
