<?php

namespace App\Http\Controllers;

use App\Models\DisabilityDaysOccupationalDiseasesByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class DisabilityDaysOccupationalDiseasesByProvinceController extends Controller
{
    /**
     * Validasyon Fonksiyonu
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
                'outpatient' => 'required|integer',
                'inpatient' => 'required|integer',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'province_id' => 'sometimes|exists:province_codes,id',
                'gender' => 'sometimes|boolean',
                'outpatient' => 'sometimes|integer',
                'inpatient' => 'sometimes|integer',
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
        $data = DisabilityDaysOccupationalDiseasesByProvince::with('province')->get();
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
        $disabilityRecord->outpatient                = $request->outpatient;
        $disabilityRecord->inpatient                 = $request->inpatient;

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

        $disabilityRecord = DisabilityDaysOccupationalDiseasesByProvince::find($id);

        if (!$disabilityRecord) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $disabilityRecord->year                      = $request->year ?? $disabilityRecord->year;
        $disabilityRecord->province_id               = $request->province_id ?? $disabilityRecord->province_id;
        $disabilityRecord->gender                    = $request->gender ?? $disabilityRecord->gender;
        $disabilityRecord->outpatient                = $request->outpatient ?? $disabilityRecord->outpatient;
        $disabilityRecord->inpatient                 = $request->inpatient ?? $disabilityRecord->inpatient;

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

        if (!$disabilityRecord) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $result = $disabilityRecord->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.']);
        }
    }
}
