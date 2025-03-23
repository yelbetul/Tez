<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\WorkstationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class WorkstationTypeController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'workstation_code' => 'required|string|max:16|unique:workstation_types,workstation_code',
                'workstation_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'workstation_code' => 'sometimes|string|max:16|unique:workstation_types,workstation_code,' . $request->id,
                'workstation_name' => 'sometimes|string|max:255',
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
        $workstationTypes = WorkstationType::all();

        if ($workstationTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $workstationTypes
        ]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $workstationType = new WorkstationType();
        $workstationType->workstation_code = $request->workstation_code;
        $workstationType->workstation_name = $request->workstation_name;

        $result = $workstationType->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İstasyon tipi kaydı başarıyla oluşturuldu.',
                'data'    => $workstationType
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $workstationType = WorkstationType::find($id);
        if (!$workstationType) {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $workstationType->workstation_code = $request->workstation_code ?? $workstationType->workstation_code;
        $workstationType->workstation_name = $request->workstation_name ?? $workstationType->workstation_name;

        $result = $workstationType->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İstasyon tipi kaydı başarıyla güncellendi.',
                'data'    => $workstationType
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $workstationType = WorkstationType::find($id);
        if (!$workstationType) {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı bulunamadı!'
            ]);
        }

        if ($workstationType->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'İstasyon tipi kaydı başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'İstasyon tipi kaydı silinirken hata oluştu.'
        ]);
    }
}
