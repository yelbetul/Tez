<?php

namespace App\Http\Controllers;

use App\Models\ProvinceCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class ProvinceCodeController extends Controller
{
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'province_code' => 'required|string|size:2|unique:province_codes,province_code',
                'province_name' => 'required|string|max:15',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'province_code' => 'sometimes|string|size:2|unique:province_codes,province_code,' . $request->id,
                'province_name' => 'sometimes|string|max:15',
            ];
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }
    public function index()
    {
        $provinceCodes = ProvinceCode::all();
        return response()->json(['success' => true, 'data' => $provinceCodes]);
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $provinceCode = new ProvinceCode();
        $provinceCode->province_code = $request->province_code;
        $provinceCode->province_name = $request->province_name;

        $result = $provinceCode->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'ProvinceCode başarıyla oluşturuldu.', 'data' => $provinceCode]);
        } else {
            return response()->json(['success' => false, 'message' => 'ProvinceCode oluşturulurken bir hata oluştu.']);
        }
    }
    public function update(Request $request, $id)
    {
        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $provinceCode = ProvinceCode::find($id);

        if (!$provinceCode) {
            return response()->json(['success' => false, 'message' => 'ProvinceCode bulunamadı.'], 404);
        }

        $provinceCode->province_code = $request->province_code ?? $provinceCode->province_code;
        $provinceCode->province_name = $request->province_name ?? $provinceCode->province_name;

        $result = $provinceCode->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'ProvinceCode başarıyla güncellendi.', 'data' => $provinceCode]);
        } else {
            return response()->json(['success' => false, 'message' => 'ProvinceCode güncellenirken bir hata oluştu.']);
        }
    }

    /**
     * İl silme
     */
    public function destroy($id)
    {
        $provinceCode = ProvinceCode::find($id);

        if (!$provinceCode) {
            return response()->json(['success' => false, 'message' => 'ProvinceCode bulunamadı.'], 404);
        }

        $provinceCode->delete();

        return response()->json(['success' => true, 'message' => 'ProvinceCode başarıyla silindi.']);
    }
}
