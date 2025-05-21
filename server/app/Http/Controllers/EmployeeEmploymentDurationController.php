<?php

namespace App\Http\Controllers;

use App\Models\EmployeeEmploymentDuration;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeEmploymentDurationController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'code'                => 'required|string|max:16|unique:employee_employment_durations,code',
                'employment_duration' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code'                => 'sometimes|string|max:16|unique:employee_employment_durations,code,' . $request->id,
                'employment_duration' => 'sometimes|string|max:255',
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
     * Tüm çalışan istihdam süresi kayıtlarını listele.
     */
    public function index()
    {
        $durations = EmployeeEmploymentDuration::all();

        if ($durations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $durations
        ]);
    }
    public function indexUser()
    {
        return DB::table('employee_employment_durations')
            ->select('code', 'employment_duration')
            ->orderBy('code')
            ->get()
            ->map(function($item) {
                return [
                    'code'                => $item->code,
                    'employment_duration' => $item->employment_duration,
                ];
            });
    }


    /**
     * Yeni çalışan istihdam süresi kaydı oluştur.
     */
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $duration = new EmployeeEmploymentDuration();
        $duration->code = $request->code;
        $duration->employment_duration = $request->employment_duration;

        if ($duration->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan istihdam süresi kaydı başarıyla oluşturuldu.',
                'data'    => $duration
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Çalışan istihdam süresi kaydı oluşturulurken hata oluştu.'
        ]);
    }

    /**
     * Çalışan istihdam süresi kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $duration = EmployeeEmploymentDuration::find($id);
        if (!$duration) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $duration->code = $request->code ?? $duration->code;
        $duration->employment_duration = $request->employment_duration ?? $duration->employment_duration;

        if ($duration->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan istihdam süresi kaydı başarıyla güncellendi.',
                'data'    => $duration
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Çalışan istihdam süresi kaydı güncellenirken hata oluştu.'
        ]);
    }

    /**
     * Çalışan istihdam süresi kaydını sil.
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $duration = EmployeeEmploymentDuration::find($id);
        if (!$duration) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı bulunamadı!'
            ]);
        }

        if ($duration->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan istihdam süresi kaydı başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Çalışan istihdam süresi kaydı silinirken hata oluştu.'
        ]);
    }
}
