<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class MonthController extends Controller
{
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'month_name' => 'required|string|max:255|unique:months,month_name',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'month_name' => 'sometimes|string|max:255|unique:months,month_name,' . $request->id,
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
        $months = Month::all();

        if ($months->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Ay kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $months
        ]);
    }

    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) {
            return $auth;
        }

        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $month = new Month();
        $month->month_name = $request->month_name;

        if ($month->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Ay başarıyla kaydedildi.',
                'data'    => $month
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Ay kaydedilirken bir hata oluştu.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) {
            return $auth;
        }

        $month = Month::find($id);
        if (!$month) {
            return response()->json([
                'success' => false,
                'message' => 'Ay bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        // İsteğe bağlı olarak, sadece gönderilen alan güncellensin:
        $month->month_name = $request->month_name ?? $month->month_name;

        if ($month->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Ay başarıyla güncellendi.',
                'data'    => $month
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Ay güncellenirken bir hata oluştu.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) {
            return $auth;
        }

        $month = Month::find($id);
        if (!$month) {
            return response()->json([
                'success' => false,
                'message' => 'Ay bulunamadı!'
            ]);
        }

        if ($month->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Ay başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Ay silinirken bir hata oluştu.'
        ]);
    }
}
