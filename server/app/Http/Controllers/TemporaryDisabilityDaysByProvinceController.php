<?php

namespace App\Http\Controllers;

use App\Models\TemporaryDisabilityDaysByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class TemporaryDisabilityDaysByProvinceController extends Controller
{
    /**
     * Validasyonların yapıldığı fonksiyon.
     */
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'province_id' => 'required|exists:province_codes,id',
                'gender' => 'required|boolean',
                'is_outpatient' => 'required|boolean',
                'is_inpatient' => 'required|boolean',
                'one_day_unfit' => 'required|integer|min:0',
                'two_days_unfit' => 'required|integer|min:0',
                'three_days_unfit' => 'required|integer|min:0',
                'four_days_unfit' => 'required|integer|min:0',
                'five_or_more_days_unfit' => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'province_id' => 'sometimes|exists:province_codes,id',
                'gender' => 'sometimes|boolean',
                'is_outpatient' => 'sometimes|boolean',
                'is_inpatient' => 'sometimes|boolean',
                'one_day_unfit' => 'sometimes|integer|min:0',
                'two_days_unfit' => 'sometimes|integer|min:0',
                'three_days_unfit' => 'sometimes|integer|min:0',
                'four_days_unfit' => 'sometimes|integer|min:0',
                'five_or_more_days_unfit' => 'sometimes|integer|min:0',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        return null;
    }

    /**
     * Tüm verileri listele.
     */
    public function index()
    {
        $data = TemporaryDisabilityDaysByProvince::all();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = TemporaryDisabilityDaysByProvince::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Province ID'ye göre verileri listele.
     */
    public function indexByProvinceId($provinceId)
    {
        $data = TemporaryDisabilityDaysByProvince::where('province_id', $provinceId)->get();
        return response()->json($data);
    }

    /**
     * Province code'a göre verileri listele.
     */
    public function indexByProvinceCode($provinceCode)
    {
        $data = TemporaryDisabilityDaysByProvince::whereHas('province', function ($query) use ($provinceCode) {
            $query->where('province_code', $provinceCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Yeni kayıt ekleme (STORE).
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $temporaryDisability = new TemporaryDisabilityDaysByProvince();
        $temporaryDisability->year                      = $request->year;
        $temporaryDisability->province_id               = $request->province_id;
        $temporaryDisability->gender                    = $request->gender;
        $temporaryDisability->is_outpatient             = $request->is_outpatient;
        $temporaryDisability->is_inpatient              = $request->is_inpatient;
        $temporaryDisability->one_day_unfit             = $request->one_day_unfit;
        $temporaryDisability->two_days_unfit            = $request->two_days_unfit;
        $temporaryDisability->three_days_unfit          = $request->three_days_unfit;
        $temporaryDisability->four_days_unfit           = $request->four_days_unfit;
        $temporaryDisability->five_or_more_days_unfit   = $request->five_or_more_days_unfit;

        $result = $temporaryDisability->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $temporaryDisability]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri kaydedilirken hata oluştu.']);
        }
    }

    /**
     * Kayıt güncelleme (UPDATE).
     */
    public function update(Request $request, $id)
    {
        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $temporaryDisability = TemporaryDisabilityDaysByProvince::find($id);

        if (!$temporaryDisability) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $temporaryDisability->year                      = $request->year ?? $temporaryDisability->year;
        $temporaryDisability->province_id               = $request->province_id ?? $temporaryDisability->province_id;
        $temporaryDisability->gender                    = $request->gender ?? $temporaryDisability->gender;
        $temporaryDisability->is_outpatient             = $request->is_outpatient ?? $temporaryDisability->is_outpatient;
        $temporaryDisability->is_inpatient              = $request->is_inpatient ?? $temporaryDisability->is_inpatient;
        $temporaryDisability->one_day_unfit             = $request->one_day_unfit ?? $temporaryDisability->one_day_unfit;
        $temporaryDisability->two_days_unfit            = $request->two_days_unfit ?? $temporaryDisability->two_days_unfit;
        $temporaryDisability->three_days_unfit          = $request->three_days_unfit ?? $temporaryDisability->three_days_unfit;
        $temporaryDisability->four_days_unfit           = $request->four_days_unfit ?? $temporaryDisability->four_days_unfit;
        $temporaryDisability->five_or_more_days_unfit   = $request->five_or_more_days_unfit ?? $temporaryDisability->five_or_more_days_unfit;

        $result = $temporaryDisability->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $temporaryDisability]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.']);
        }
    }

    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $temporaryDisability = TemporaryDisabilityDaysByProvince::find($id);

        if (!$temporaryDisability) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $result = $temporaryDisability->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.']);
        }
    }
}
