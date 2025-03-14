<?php

namespace App\Http\Controllers;

use App\Models\InjuryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class InjuryTypeController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'injury_code' => 'required|string|max:255|unique:injury_types,injury_code',
                'group_code' => 'required|string|max:255',
                'group_name' => 'required|string|max:255',
                'sub_group_code' => 'required|string|max:255',
                'sub_group_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'injury_code' => 'sometimes|string|max:255|unique:injury_types,injury_code,' . $request->id,
                'group_code' => 'sometimes|string|max:255',
                'group_name' => 'sometimes|string|max:255',
                'sub_group_code' => 'sometimes|string|max:255',
                'sub_group_name' => 'sometimes|string|max:255',
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
        $injuryTypes = InjuryType::all();
        return response()->json(['success' => true, 'data' => $injuryTypes]);
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $injuryType = new InjuryType();
        $injuryType->injury_code = $request->injury_code;
        $injuryType->group_code = $request->group_code;
        $injuryType->group_name = $request->group_name;
        $injuryType->sub_group_code = $request->sub_group_code;
        $injuryType->sub_group_name = $request->sub_group_name;

        $result = $injuryType->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Yara Türü başarıyla kaydedildi.', 'data' => $injuryType]);
        } else {
            return response()->json(['success' => false, 'message' => 'Yara Türü kaydedilirken hata oluştu.']);
        }
    }

    public function update(Request $request, $id)
    {
        $injuryType = InjuryType::find($id);
        if (!$injuryType) {
            return response()->json(['success' => false, 'message' => 'Yara Türü bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $injuryType->injury_code = $request->injury_code;
        $injuryType->group_code = $request->group_code;
        $injuryType->group_name = $request->group_name;
        $injuryType->sub_group_code = $request->sub_group_code;
        $injuryType->sub_group_name = $request->sub_group_name;

        $result = $injuryType->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Yara Türü başarıyla güncellendi.', 'data' => $injuryType]);
        } else {
            return response()->json(['success' => false, 'message' => 'Yara Türü güncellenirken hata oluştu.']);
        }
    }

    public function destroy($id)
    {

        $injuryType = InjuryType::find($id);
        if (!$injuryType) {
            return response()->json(['success' => false, 'message' => 'Yara Türü bulunamadı!']);
        }

        $result = $injuryType->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Yara Türü başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Yara Türü silinirken hata oluştu.']);
        }
    }
}
