<?php

namespace App\Http\Controllers;

use App\Models\SpecialActivitiesBeforeAccident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class SpecialActivitiesBeforeAccidentController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'special_activity_code' => 'required|string|max:16|unique:special_activities_before_accidents,special_activity_code',
                'group_code'            => 'required|string|max:3',
                'group_name'            => 'required|string|max:255',
                'sub_group_code'        => 'required|string|max:3',
                'sub_group_name'        => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'special_activity_code' => 'sometimes|string|max:16|unique:special_activities_before_accidents,special_activity_code,' . $request->id,
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

    /**
     * Tüm özel aktivite kayıtlarını listele.
     */
    public function index()
    {
        $specialActivities = SpecialActivitiesBeforeAccident::all();
        return response()->json(['success' => true, 'data' => $specialActivities]);
    }

    /**
     * Yeni özel aktivite kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $specialActivity = new SpecialActivitiesBeforeAccident();
        $specialActivity->special_activity_code = $request->special_activity_code;
        $specialActivity->group_code            = $request->group_code;
        $specialActivity->group_name            = $request->group_name;
        $specialActivity->sub_group_code        = $request->sub_group_code;
        $specialActivity->sub_group_name        = $request->sub_group_name;

        $result = $specialActivity->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Özel aktivite kaydı başarıyla oluşturuldu.',
                'data'    => $specialActivity
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Özel aktivite kaydı kaydedilirken hata oluştu.'
            ]);
        }
    }

    /**
     * Özel aktivite kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $specialActivity = SpecialActivitiesBeforeAccident::find($id);
        if (!$specialActivity) {
            return response()->json(['success' => false, 'message' => 'Özel aktivite kaydı bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $specialActivity->special_activity_code = $request->special_activity_code ?? $specialActivity->special_activity_code;
        $specialActivity->group_code            = $request->group_code ?? $specialActivity->group_code;
        $specialActivity->group_name            = $request->group_name ?? $specialActivity->group_name;
        $specialActivity->sub_group_code        = $request->sub_group_code ?? $specialActivity->sub_group_code;
        $specialActivity->sub_group_name        = $request->sub_group_name ?? $specialActivity->sub_group_name;

        $result = $specialActivity->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Özel aktivite kaydı başarıyla güncellendi.',
                'data'    => $specialActivity
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Özel aktivite kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Özel aktivite kaydını sil.
     */
    public function destroy($id)
    {
        $specialActivity = SpecialActivitiesBeforeAccident::find($id);
        if (!$specialActivity) {
            return response()->json(['success' => false, 'message' => 'Özel aktivite kaydı bulunamadı!']);
        }

        $specialActivity->delete();
        return response()->json(['success' => true, 'message' => 'Özel aktivite kaydı başarıyla silindi.']);
    }
}
