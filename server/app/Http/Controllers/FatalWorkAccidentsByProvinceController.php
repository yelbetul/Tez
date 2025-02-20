<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FatalWorkAccidentsByProvinceController extends Controller
{
    /**
     * Validasyonların yapıldığı fonksiyon.
     */
    public function validateRequest($request, $type = 'store')
    {
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'province_id' => 'required|exists:province_codes,id',
                'gender' => 'required|boolean',
                'work_accident_fatalities' => 'required|integer|min:0',
                'occupational_disease_fatalities' => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'province_id' => 'sometimes|exists:province_codes,id',
                'gender' => 'sometimes|boolean',
                'work_accident_fatalities' => 'sometimes|integer|min:0',
                'occupational_disease_fatalities' => 'sometimes|integer|min:0',
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
        $data = FatalWorkAccidentsByProvince::all();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = FatalWorkAccidentsByProvince::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Province ID'ye göre verileri listele.
     */
    public function indexByProvinceId($provinceId)
    {
        $data = FatalWorkAccidentsByProvince::where('province_id', $provinceId)->get();
        return response()->json($data);
    }

    /**
     * Province code'a göre verileri listele.
     */
    public function indexByProvinceCode($provinceCode)
    {
        $data = FatalWorkAccidentsByProvince::whereHas('province', function ($query) use ($provinceCode) {
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

        $record = new FatalWorkAccidentsByProvince();
        $record->year = $request->year;
        $record->province_id = $request->province_id;
        $record->gender = $request->gender;
        $record->work_accident_fatalities = $request->work_accident_fatalities;
        $record->occupational_disease_fatalities = $request->occupational_disease_fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $record]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri kaydedilirken hata oluştu.'], 500);
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

        $record = FatalWorkAccidentsByProvince::find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.'], 404);
        }

        $record->year = $request->year ?? $record->year;
        $record->province_id = $request->province_id ?? $record->province_id;
        $record->gender = $request->gender ?? $record->gender;
        $record->work_accident_fatalities = $request->work_accident_fatalities ?? $record->work_accident_fatalities;
        $record->occupational_disease_fatalities = $request->occupational_disease_fatalities ?? $record->occupational_disease_fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $record]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.'], 500);
        }
    }

    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $record = FatalWorkAccidentsByProvince::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.'], 404);
        }

        $result = $record->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.'], 500);
        }
    }
}
