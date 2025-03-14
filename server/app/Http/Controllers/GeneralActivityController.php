<?php

namespace App\Http\Controllers;

use App\Models\GeneralActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class GeneralActivityController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'general_activity_code' => 'required|string|max:16|unique:general_activities,general_activity_code',
                'group_code'            => 'required|string|max:3',
                'group_name'            => 'required|string|max:255',
                'sub_group_code'        => 'required|string|max:3',
                'sub_group_name'        => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'general_activity_code' => 'sometimes|string|max:16|unique:general_activities,general_activity_code,' . $request->id,
                'group_code'            => 'sometimes|string|max:3',
                'group_name'            => 'sometimes|string|max:255',
                'sub_group_code'        => 'sometimes|string|max:3',
                'sub_group_name'        => 'sometimes|string|max:255',
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
        $generalActivities = GeneralActivity::all();
        return response()->json(['success' => true, 'data' => $generalActivities]);
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $generalActivity = new GeneralActivity();
        $generalActivity->general_activity_code = $request->general_activity_code;
        $generalActivity->group_code = $request->group_code;
        $generalActivity->group_name = $request->group_name;
        $generalActivity->sub_group_code = $request->sub_group_code;
        $generalActivity->sub_group_name = $request->sub_group_name;

        $result = $generalActivity->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Genel aktivite başarıyla kaydedildi.', 'data' => $generalActivity]);
        } else {
            return response()->json(['success' => false, 'message' => 'Genel aktivite kaydedilirken hata oluştu.']);
        }
    }

    public function update(Request $request, $id)
    {
        $generalActivity = GeneralActivity::find($id);
        if (!$generalActivity) {
            return response()->json(['success' => false, 'message' => 'Genel aktivite bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $generalActivity->general_activity_code = $request->general_activity_code ?? $generalActivity->general_activity_code;
        $generalActivity->group_code = $request->group_code ?? $generalActivity->group_code;
        $generalActivity->group_name = $request->group_name ?? $generalActivity->group_name;
        $generalActivity->sub_group_code = $request->sub_group_code ?? $generalActivity->sub_group_code;
        $generalActivity->sub_group_name = $request->sub_group_name ?? $generalActivity->sub_group_name;

        $result = $generalActivity->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Genel aktivite başarıyla güncellendi.', 'data' => $generalActivity]);
        } else {
            return response()->json(['success' => false, 'message' => 'Genel aktivite güncellenirken hata oluştu.']);
        }
    }

    public function destroy($id)
    {
        $generalActivity = GeneralActivity::find($id);
        if (!$generalActivity) {
            return response()->json(['success' => false, 'message' => 'Genel aktivite bulunamadı!']);
        }

        $generalActivity->delete();
        return response()->json(['success' => true, 'message' => 'Genel aktivite başarıyla silindi.']);
    }
}
