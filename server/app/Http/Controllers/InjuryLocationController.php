<?php

namespace App\Http\Controllers;

use App\Models\InjuryLocation;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InjuryLocationController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'injury_location_code' => 'required|string|max:16|unique:injury_locations,injury_location_code',
                'group_code'           => 'required|string|max:3',
                'group_name'           => 'required|string|max:255',
                'sub_group_code'       => 'required|string|max:3',
                'sub_group_name'       => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'injury_location_code' => 'sometimes|string|max:16|unique:injury_locations,injury_location_code,' . $request->id,
                'group_code'           => 'sometimes|string|max:3',
                'group_name'           => 'sometimes|string|max:255',
                'sub_group_code'       => 'sometimes|string|max:3',
                'sub_group_name'       => 'sometimes|string|max:255',
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

    /**
     * Tüm kayıtları listele.
     */
    public function index()
    {
        $injuryLocations = InjuryLocation::all();

        if ($injuryLocations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        return response()->json(['success' => true, 'data' => $injuryLocations]);
    }

    public function indexUser()
    {
        return DB::table('injury_locations')
            ->select(
                'injury_location_code',
                'group_code',
                'group_name',
                'sub_group_code',
                'sub_group_name'
            )
            ->orderBy('group_code')
            ->orderBy('sub_group_code')
            ->orderBy('injury_location_code')
            ->get()
            ->map(function ($item) {
                return [
                    'injury_location_code' => $item->injury_location_code,
                    'group_code' => $item->group_code,
                    'group_name' => $item->group_name,
                    'sub_group_code' => $item->sub_group_code,
                    'sub_group_name' => $item->sub_group_name
                ];
            });
    }

    /**
     * Yeni kayıt oluştur.
     */
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $injuryLocation = new InjuryLocation();
        $injuryLocation->injury_location_code = $request->injury_location_code;
        $injuryLocation->group_code = $request->group_code;
        $injuryLocation->group_name = $request->group_name;
        $injuryLocation->sub_group_code = $request->sub_group_code;
        $injuryLocation->sub_group_name = $request->sub_group_name;

        if ($injuryLocation->save()) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla oluşturuldu.', 'data' => $injuryLocation]);
        }

        return response()->json(['success' => false, 'message' => 'Kayıt oluşturulurken hata oluştu.']);
    }

    /**
     * Kaydı güncelle.
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $injuryLocation = InjuryLocation::find($id);
        if (!$injuryLocation) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $injuryLocation->injury_location_code = $request->injury_location_code ?? $injuryLocation->injury_location_code;
        $injuryLocation->group_code = $request->group_code ?? $injuryLocation->group_code;
        $injuryLocation->group_name = $request->group_name ?? $injuryLocation->group_name;
        $injuryLocation->sub_group_code = $request->sub_group_code ?? $injuryLocation->sub_group_code;
        $injuryLocation->sub_group_name = $request->sub_group_name ?? $injuryLocation->sub_group_name;

        if ($injuryLocation->save()) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla güncellendi.', 'data' => $injuryLocation]);
        }

        return response()->json(['success' => false, 'message' => 'Kayıt güncellenirken hata oluştu.']);
    }

    /**
     * Kaydı sil.
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $injuryLocation = InjuryLocation::find($id);
        if (!$injuryLocation) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı!']);
        }

        if ($injuryLocation->delete()) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        }

        return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.']);
    }
}
