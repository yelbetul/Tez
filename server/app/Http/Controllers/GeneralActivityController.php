<?php

namespace App\Http\Controllers;

use App\Models\GeneralActivity;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GeneralActivityController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'general_activity_code' => 'required|string|max:16|unique:general_activities,general_activity_code',
                'group_code'            => 'required|string|max:3',
                'group_name'            => 'required|string|max:255',
                'sub_group_code'        => 'required|string|max:3',
                'sub_group_name'        => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'general_activity_code' => 'sometimes|string|max:16|unique:general_activities,general_activity_code,' . $request->id,
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
    public function indexUser()
    {
        return DB::table('general_activities')
            ->select(
                'general_activity_code',
                'group_code',
                'group_name',
                'sub_group_code',
                'sub_group_name'
            )
            ->orderBy('group_code')
            ->orderBy('sub_group_code')
            ->orderBy('general_activity_code')
            ->get()
            ->map(function ($item) {
                return [
                    'general_activity_code' => $item->general_activity_code,
                    'group_code' => $item->group_code,
                    'group_name' => $item->group_name,
                    'sub_group_code' => $item->sub_group_code,
                    'sub_group_name' => $item->sub_group_name
                ];
            });
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
        $generalActivities = GeneralActivity::all();

        if ($generalActivities->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Genel aktivite kaydı bulunamadı.']);
        }

        return response()->json(['success' => true, 'data' => $generalActivities]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $generalActivity = new GeneralActivity();
        $generalActivity->general_activity_code = $request->general_activity_code;
        $generalActivity->group_code = $request->group_code;
        $generalActivity->group_name = $request->group_name;
        $generalActivity->sub_group_code = $request->sub_group_code;
        $generalActivity->sub_group_name = $request->sub_group_name;

        if ($generalActivity->save()) {
            return response()->json(['success' => true, 'message' => 'Genel aktivite başarıyla kaydedildi.', 'data' => $generalActivity]);
        }

        return response()->json(['success' => false, 'message' => 'Genel aktivite kaydedilirken hata oluştu.']);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $generalActivity = GeneralActivity::find($id);
        if (!$generalActivity) {
            return response()->json(['success' => false, 'message' => 'Genel aktivite bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $generalActivity->general_activity_code = $request->general_activity_code ?? $generalActivity->general_activity_code;
        $generalActivity->group_code = $request->group_code ?? $generalActivity->group_code;
        $generalActivity->group_name = $request->group_name ?? $generalActivity->group_name;
        $generalActivity->sub_group_code = $request->sub_group_code ?? $generalActivity->sub_group_code;
        $generalActivity->sub_group_name = $request->sub_group_name ?? $generalActivity->sub_group_name;

        if ($generalActivity->save()) {
            return response()->json(['success' => true, 'message' => 'Genel aktivite başarıyla güncellendi.', 'data' => $generalActivity]);
        }

        return response()->json(['success' => false, 'message' => 'Genel aktivite güncellenirken hata oluştu.']);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $generalActivity = GeneralActivity::find($id);
        if (!$generalActivity) {
            return response()->json(['success' => false, 'message' => 'Genel aktivite bulunamadı!']);
        }

        if ($generalActivity->delete()) {
            return response()->json(['success' => true, 'message' => 'Genel aktivite başarıyla silindi.']);
        }

        return response()->json(['success' => false, 'message' => 'Genel aktivite silinirken hata oluştu.']);
    }
}
