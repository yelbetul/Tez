<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\WorkEnvironment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class WorkEnvironmentController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'environment_code' => 'required|string|max:16|unique:work_environments,environment_code',
                'group_code'       => 'required|string|max:3',
                'group_name'       => 'required|string|max:255',
                'sub_group_code'   => 'required|string|max:3',
                'sub_group_name'   => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'environment_code' => 'sometimes|string|max:16|unique:work_environments,environment_code,' . $request->id,
                'group_code'       => 'sometimes|string|max:3',
                'group_name'       => 'sometimes|string|max:255',
                'sub_group_code'   => 'sometimes|string|max:3',
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
        $workEnvironments = WorkEnvironment::all();

        if ($workEnvironments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $workEnvironments
        ]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $workEnvironment = new WorkEnvironment();
        $workEnvironment->environment_code = $request->environment_code;
        $workEnvironment->group_code       = $request->group_code;
        $workEnvironment->group_name       = $request->group_name;
        $workEnvironment->sub_group_code   = $request->sub_group_code;
        $workEnvironment->sub_group_name   = $request->sub_group_name;

        $result = $workEnvironment->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş ortamı kaydı başarıyla oluşturuldu.',
                'data'    => $workEnvironment
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'İş ortamı kaydı oluşturulurken hata oluştu.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $workEnvironment = WorkEnvironment::find($id);
        if (!$workEnvironment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $workEnvironment->environment_code = $request->environment_code ?? $workEnvironment->environment_code;
        $workEnvironment->group_code       = $request->group_code ?? $workEnvironment->group_code;
        $workEnvironment->group_name       = $request->group_name ?? $workEnvironment->group_name;
        $workEnvironment->sub_group_code   = $request->sub_group_code ?? $workEnvironment->sub_group_code;
        $workEnvironment->sub_group_name   = $request->sub_group_name ?? $workEnvironment->sub_group_name;

        $result = $workEnvironment->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş ortamı kaydı başarıyla güncellendi.',
                'data'    => $workEnvironment
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'İş ortamı kaydı güncellenirken hata oluştu.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $workEnvironment = WorkEnvironment::find($id);
        if (!$workEnvironment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı bulunamadı!'
            ]);
        }

        if ($workEnvironment->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'İş ortamı kaydı başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'İş ortamı kaydı silinirken hata oluştu.'
        ]);
    }
}
