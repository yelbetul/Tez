<?php

namespace App\Http\Controllers;

use App\Models\EmployeeGroup;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeGroupController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'code'           => 'required|string|max:16|unique:employee_groups,code',
                'employee_count' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code'           => 'sometimes|string|max:16|unique:employee_groups,code,' . $request->id,
                'employee_count' => 'sometimes|string|max:255',
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

    /**
     * Tüm çalışan grubu kayıtlarını listele.
     */
    public function index()
    {
        $employeeGroups = EmployeeGroup::all();

        if ($employeeGroups->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $employeeGroups
        ]);
    }
    public function indexUser()
    {
        return DB::table('employee_groups')
            ->select(
                'code',
                'employee_count'
            )
            ->orderBy('code')
            ->get()
            ->map(function($item){
                return [
                    'code' => $item->code,
                    'employee_count' => $item->employee_count,
                ];
            });
    }

    /**
     * Yeni çalışan grubu kaydı oluştur.
     */
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $employeeGroup = new EmployeeGroup();
        $employeeGroup->code = $request->code;
        $employeeGroup->employee_count = $request->employee_count;

        if ($employeeGroup->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan grubu kaydı başarıyla oluşturuldu.',
                'data'    => $employeeGroup
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Çalışan grubu kaydı oluşturulurken hata oluştu.'
        ]);
    }

    /**
     * Çalışan grubu kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $employeeGroup = EmployeeGroup::find($id);
        if (!$employeeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $employeeGroup->code = $request->code ?? $employeeGroup->code;
        $employeeGroup->employee_count = $request->employee_count ?? $employeeGroup->employee_count;

        if ($employeeGroup->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan grubu kaydı başarıyla güncellendi.',
                'data'    => $employeeGroup
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Çalışan grubu kaydı güncellenirken hata oluştu.'
        ]);
    }

    /**
     * Çalışan grubu kaydını sil.
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $employeeGroup = EmployeeGroup::find($id);
        if (!$employeeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu bulunamadı!'
            ]);
        }

        if ($employeeGroup->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan grubu kaydı başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Çalışan grubu kaydı silinirken hata oluştu.'
        ]);
    }
}
