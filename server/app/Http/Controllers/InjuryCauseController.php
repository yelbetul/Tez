<?php

namespace App\Http\Controllers;

use App\Models\InjuryCause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class InjuryCauseController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'injury_cause_code' => 'required|string|max:16|unique:injury_causes,injury_cause_code',
                'group_code'        => 'required|string|max:3',
                'group_name'        => 'required|string|max:255',
                'sub_group_code'    => 'required|string|max:3',
                'sub_group_name'    => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'injury_cause_code' => 'sometimes|string|max:16|unique:injury_causes,injury_cause_code,' . $request->id,
                'group_code'        => 'sometimes|string|max:3',
                'group_name'        => 'sometimes|string|max:255',
                'sub_group_code'    => 'sometimes|string|max:3',
                'sub_group_name'    => 'sometimes|string|max:255',
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
        $injuryCauses = InjuryCause::all();
        return response()->json(['success' => true, 'data' => $injuryCauses]);
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $injuryCause = new InjuryCause();
        $injuryCause->injury_cause_code = $request->injury_cause_code;
        $injuryCause->group_code        = $request->group_code;
        $injuryCause->group_name        = $request->group_name;
        $injuryCause->sub_group_code    = $request->sub_group_code;
        $injuryCause->sub_group_name    = $request->sub_group_name;

        if ($injuryCause->save()) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla oluşturuldu.', 'data' => $injuryCause]);
        }

        return response()->json(['success' => false, 'message' => 'Kayıt oluşturulurken hata oluştu.']);
    }

    public function update(Request $request, $id)
    {
        $injuryCause = InjuryCause::find($id);
        if (!$injuryCause) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $injuryCause->injury_cause_code = $request->injury_cause_code ?? $injuryCause->injury_cause_code;
        $injuryCause->group_code        = $request->group_code ?? $injuryCause->group_code;
        $injuryCause->group_name        = $request->group_name ?? $injuryCause->group_name;
        $injuryCause->sub_group_code    = $request->sub_group_code ?? $injuryCause->sub_group_code;
        $injuryCause->sub_group_name    = $request->sub_group_name ?? $injuryCause->sub_group_name;

        if ($injuryCause->save()) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla güncellendi.', 'data' => $injuryCause]);
        }

        return response()->json(['success' => false, 'message' => 'Kayıt güncellenirken hata oluştu.']);
    }

    public function destroy($id)
    {
        $injuryCause = InjuryCause::find($id);
        if (!$injuryCause) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı!']);
        }

        $injuryCause->delete();
        return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
    }
}
