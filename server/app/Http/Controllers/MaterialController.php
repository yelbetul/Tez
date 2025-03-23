<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'material_code'  => 'required|string|max:16|unique:materials,material_code',
                'group_code'     => 'required|string|max:3',
                'group_name'     => 'required|string|max:255',
                'sub_group_code' => 'required|string|max:3',
                'sub_group_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'material_code'  => 'sometimes|string|max:16|unique:materials,material_code,' . $request->id,
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
     * Tüm malzeme kayıtlarını listele.
     */
    public function index()
    {
        $materials = Material::all();

        if ($materials->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı bulunamadı.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $materials
        ]);
    }

    /**
     * Yeni malzeme kaydı oluştur.
     */
    public function store(Request $request)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $material = new Material();
        $material->material_code   = $request->material_code;
        $material->group_code      = $request->group_code;
        $material->group_name      = $request->group_name;
        $material->sub_group_code  = $request->sub_group_code;
        $material->sub_group_name  = $request->sub_group_name;

        if ($material->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Malzeme kaydı başarıyla oluşturuldu.',
                'data'    => $material
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Malzeme kaydı oluşturulurken hata oluştu.'
        ]);
    }

    /**
     * Malzeme kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $material->material_code   = $request->material_code ?? $material->material_code;
        $material->group_code      = $request->group_code ?? $material->group_code;
        $material->group_name      = $request->group_name ?? $material->group_name;
        $material->sub_group_code  = $request->sub_group_code ?? $material->sub_group_code;
        $material->sub_group_name  = $request->sub_group_name ?? $material->sub_group_name;

        if ($material->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Malzeme kaydı başarıyla güncellendi.',
                'data'    => $material
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Malzeme kaydı güncellenirken hata oluştu.'
        ]);
    }

    /**
     * Malzeme kaydını sil.
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authenticate($request);
        if ($auth instanceof \Illuminate\Http\JsonResponse) return $auth;

        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı bulunamadı!'
            ]);
        }

        if ($material->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Malzeme kaydı başarıyla silindi.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Malzeme kaydı silinirken hata oluştu.'
        ]);
    }
}
