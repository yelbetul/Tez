<?php

namespace App\Http\Controllers;

use App\Models\DisabilityDaysOccupationalDiseasesByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class DisabilityDaysOccupationalDiseasesByProvinceController extends Controller
{
    /**
     * Validasyonların yapıldığı fonksiyon.
     */
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        // Store ve update için kurallar
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

        // Validator oluştur
        $validator = Validator::make($request->all(), $rules);

        // Hata varsa döndür
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
        $data = DisabilityDaysOccupationalDiseasesByProvince::all();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = DisabilityDaysOccupationalDiseasesByProvince::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Province ID'ye göre verileri listele.
     */
    public function indexByProvinceId($provinceId)
    {
        $data = DisabilityDaysOccupationalDiseasesByProvince::where('province_id', $provinceId)->get();
        return response()->json($data);
    }

    /**
     * Province code'a göre verileri listele.
     */
    public function indexByProvinceCode($provinceCode)
    {
        $data = DisabilityDaysOccupationalDiseasesByProvince::whereHas('province', function ($query) use ($provinceCode) {
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

        $disabilityRecord = new DisabilityDaysOccupationalDiseasesByProvince();
        $disabilityRecord->year                      = $request->year;
        $disabilityRecord->province_id               = $request->province_id;
        $disabilityRecord->gender                    = $request->gender;
        $disabilityRecord->is_outpatient             = $request->is_outpatient;
        $disabilityRecord->is_inpatient              = $request->is_inpatient;
        $disabilityRecord->one_day_unfit             = $request->one_day_unfit;
        $disabilityRecord->two_days_unfit            = $request->two_days_unfit;
        $disabilityRecord->three_days_unfit          = $request->three_days_unfit;
        $disabilityRecord->four_days_unfit           = $request->four_days_unfit;
        $disabilityRecord->five_or_more_days_unfit   = $request->five_or_more_days_unfit;

        $result = $disabilityRecord->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $disabilityRecord]);
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

        $disabilityRecord = DisabilityDaysOccupationalDiseasesByProvince::findOrFail($id);

        $disabilityRecord->year                      = $request->year ?? $disabilityRecord->year;
        $disabilityRecord->province_id               = $request->province_id ?? $disabilityRecord->province_id;
        $disabilityRecord->gender                    = $request->gender ?? $disabilityRecord->gender;
        $disabilityRecord->is_outpatient             = $request->is_outpatient ?? $disabilityRecord->is_outpatient;
        $disabilityRecord->is_inpatient              = $request->is_inpatient ?? $disabilityRecord->is_inpatient;
        $disabilityRecord->one_day_unfit             = $request->one_day_unfit ?? $disabilityRecord->one_day_unfit;
        $disabilityRecord->two_days_unfit            = $request->two_days_unfit ?? $disabilityRecord->two_days_unfit;
        $disabilityRecord->three_days_unfit          = $request->three_days_unfit ?? $disabilityRecord->three_days_unfit;
        $disabilityRecord->four_days_unfit           = $request->four_days_unfit ?? $disabilityRecord->four_days_unfit;
        $disabilityRecord->five_or_more_days_unfit   = $request->five_or_more_days_unfit ?? $disabilityRecord->five_or_more_days_unfit;

        $result = $disabilityRecord->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $disabilityRecord]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.']);
        }
    }
    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $disabilityRecord = DisabilityDaysOccupationalDiseasesByProvince::find($id);

        // Kayıt bulunamadıysa hata döndür
        if (!$disabilityRecord) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.'], 404);
        }

        // Kayıt silme işlemi
        $result = $disabilityRecord->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.']);
        }
    }
}
