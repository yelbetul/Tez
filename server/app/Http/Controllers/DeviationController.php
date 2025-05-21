<?php

namespace App\Http\Controllers;

use App\Models\Deviation;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeviationController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'deviation_code' => 'required|string|max:16|unique:deviations,deviation_code',
                'group_code'     => 'required|string|max:3',
                'group_name'     => 'required|string|max:255',
                'sub_group_code' => 'required|string|max:3',
                'sub_group_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'deviation_code' => 'sometimes|string|max:16|unique:deviations,deviation_code,' . $request->id,
                'group_code'     => 'sometimes|string|max:3',
                'group_name'     => 'sometimes|string|max:255',
                'sub_group_code' => 'sometimes|string|max:3',
                'sub_group_name' => 'sometimes|string|max:255',
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
     * Tüm sapma (deviation) kayıtlarını listele.
     */
    public function index()
    {
        $deviations = Deviation::all();

        if ($deviations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $deviations
        ]);
    }

    public function indexUser()
    {
        return DB::table('deviations')
            ->select(
                'deviation_code',
                'group_code',
                'group_name',
                'sub_group_code',
                'sub_group_name'
            )
            ->orderBy('group_code')
            ->orderBy('sub_group_code')
            ->orderBy('deviation_code')
            ->get()
            ->map(function ($item) {
                return [
                    'deviation_code' => $item->deviation_code,
                    'group_code' => $item->group_code,
                    'group_name' => $item->group_name,
                    'sub_group_code' => $item->sub_group_code,
                    'sub_group_name' => $item->sub_group_name
                ];
            });
    }
    /**
     * Yeni sapma (deviation) kaydı oluştur.
     */
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $deviation = new Deviation();
        $deviation->deviation_code = $request->deviation_code;
        $deviation->group_code     = $request->group_code;
        $deviation->group_name     = $request->group_name;
        $deviation->sub_group_code = $request->sub_group_code;
        $deviation->sub_group_name = $request->sub_group_name;

        if ($deviation->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Sapma kaydı başarıyla oluşturuldu.',
                'data' => $deviation
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sapma kaydı kaydedilirken hata oluştu.'
        ]);
    }

    /**
     * Sapma kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $deviation = Deviation::find($id);
        if (!$deviation) {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $deviation->deviation_code = $request->deviation_code ?? $deviation->deviation_code;
        $deviation->group_code     = $request->group_code ?? $deviation->group_code;
        $deviation->group_name     = $request->group_name ?? $deviation->group_name;
        $deviation->sub_group_code = $request->sub_group_code ?? $deviation->sub_group_code;
        $deviation->sub_group_name = $request->sub_group_name ?? $deviation->sub_group_name;

        if ($deviation->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Sapma kaydı başarıyla güncellendi.',
                'data' => $deviation
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sapma kaydı güncellenirken hata oluştu.'
        ]);
    }

    /**
     * Sapma kaydını sil.
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $deviation = Deviation::find($id);
        if (!$deviation) {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı bulunamadı!'
            ]);
        }

        if ($deviation->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Sapma kaydı başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sapma kaydı silinirken hata oluştu.'
        ]);
    }
}
