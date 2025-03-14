<?php

namespace App\Http\Controllers;

use App\Models\OccupationGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class OccupationGroupController extends Controller
{
    /**
     * Validasyon fonksiyonu
     */
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'code' => 'required|string|max:16|unique:occupation_groups,code',
                'occupation_code' => 'required|string|max:16',
                'occupation_name' => 'required|string|max:16',
                'group_code' => 'required|string|max:3',
                'group_name' => 'required|string|max:255',
                'sub_group_code' => 'required|string|max:3',
                'sub_group_name' => 'required|string|max:255',
                'pure_code' => 'required|string|max:3',
                'pure_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code' => 'sometimes|string|max:16|unique:occupation_groups,code,' . $request->id,
                'occupation_code' => 'sometimes|string|max:16',
                'occupation_name' => 'sometimes|string|max:16',
                'group_code' => 'sometimes|string|max:3',
                'group_name' => 'sometimes|string|max:255',
                'sub_group_code' => 'sometimes|string|max:3',
                'sub_group_name' => 'sometimes|string|max:255',
                'pure_code' => 'sometimes|string|max:3',
                'pure_name' => 'sometimes|string|max:255',
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
        $occupationGroups = OccupationGroup::all();
        return response()->json(['success' => true, 'data' => $occupationGroups]);
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $occupationGroup = new OccupationGroup();
        $occupationGroup->code = $request->code;
        $occupationGroup->occupation_code = $request->occupation_code;
        $occupationGroup->occupation_name = $request->occupation_name;
        $occupationGroup->group_code = $request->group_code;
        $occupationGroup->group_name = $request->group_name;
        $occupationGroup->sub_group_code = $request->sub_group_code;
        $occupationGroup->sub_group_name = $request->sub_group_name;
        $occupationGroup->pure_code = $request->pure_code;
        $occupationGroup->pure_name = $request->pure_name;

        $result = $occupationGroup->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Meslek Grubu başarıyla kaydedildi.', 'data' => $occupationGroup]);
        } else {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu kaydedilirken hata oluştu.']);
        }
    }

    public function update(Request $request, $id)
    {
        $occupationGroup = OccupationGroup::find($id);
        if (!$occupationGroup) {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $occupationGroup->code = $request->code;
        $occupationGroup->occupation_code = $request->occupation_code;
        $occupationGroup->occupation_name = $request->occupation_name;
        $occupationGroup->group_code = $request->group_code;
        $occupationGroup->group_name = $request->group_name;
        $occupationGroup->sub_group_code = $request->sub_group_code;
        $occupationGroup->sub_group_name = $request->sub_group_name;
        $occupationGroup->pure_code = $request->pure_code;
        $occupationGroup->pure_name = $request->pure_name;

        $result = $occupationGroup->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Meslek Grubu başarıyla güncellendi.', 'data' => $occupationGroup]);
        } else {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu güncellenirken hata oluştu.']);
        }
    }

    public function destroy($id)
    {

        $occupationGroup = OccupationGroup::find($id);
        if (!$occupationGroup) {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu bulunamadı!']);
        }

        $result = $occupationGroup->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Meslek Grubu başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Meslek Grubu silinirken hata oluştu.']);
        }
    }
}
