<?php

namespace App\Http\Controllers;

use App\Models\Material;
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
                'material_code' => 'required|string|max:16|unique:materials,material_code',
                'group_code'    => 'required|string|max:3',
                'group_name'    => 'required|string|max:255',
                'sub_group_code'=> 'required|string|max:3',
                'sub_group_name'=> 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'material_code' => 'sometimes|string|max:16|unique:materials,material_code,' . $request->id,
                'group_code'    => 'sometimes|string|max:3',
                'group_name'    => 'sometimes|string|max:255',
                'sub_group_code'=> 'sometimes|string|max:3',
                'sub_group_name'=> 'sometimes|string|max:255',
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

    /**
     * Tüm malzeme kayıtlarını listele.
     */
    public function index()
    {
        $materials = Material::all();
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
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $material = new Material();
        $material->material_code = $request->material_code;
        $material->group_code    = $request->group_code;
        $material->group_name    = $request->group_name;
        $material->sub_group_code = $request->sub_group_code;
        $material->sub_group_name = $request->sub_group_name;

        $result = $material->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Malzeme kaydı başarıyla oluşturuldu.',
                'data'    => $material
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Malzeme kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $material->material_code = $request->material_code ?? $material->material_code;
        $material->group_code    = $request->group_code ?? $material->group_code;
        $material->group_name    = $request->group_name ?? $material->group_name;
        $material->sub_group_code = $request->sub_group_code ?? $material->sub_group_code;
        $material->sub_group_name = $request->sub_group_name ?? $material->sub_group_name;

        $result = $material->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Malzeme kaydı başarıyla güncellendi.',
                'data'    => $material
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Malzeme kaydını sil.
     */
    public function destroy($id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Malzeme kaydı bulunamadı!'
            ]);
        }

        $material->delete();
        return response()->json([
            'success' => true,
            'message' => 'Malzeme kaydı başarıyla silindi.'
        ]);
    }
}
