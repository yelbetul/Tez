<?php

namespace App\Http\Controllers;

use App\Models\WorkAccidentsByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class WorkAccidentsByProvinceController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        // Dil ayarını Türkçe yap
        App::setLocale('tr');

        // Store ve update için kurallar
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'province_id' => 'required|exists:province_codes,id', // Province_id için validasyon
                'gender' => 'required|boolean',
                'works_on_accident_day' => 'required|integer|min:0',
                'unfit_on_accident_day' => 'required|integer|min:0',
                'two_days_unfit' => 'required|integer|min:0',
                'three_days_unfit' => 'required|integer|min:0',
                'four_days_unfit' => 'required|integer|min:0',
                'five_or_more_days_unfit' => 'required|integer|min:0',
                'occupational_disease_cases' => 'required|integer|min:0|max:127',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'province_id' => 'sometimes|exists:province_codes,id', // Province_id için validasyon
                'gender' => 'sometimes|boolean',
                'works_on_accident_day' => 'sometimes|integer|min:0',
                'unfit_on_accident_day' => 'sometimes|integer|min:0',
                'two_days_unfit' => 'sometimes|integer|min:0',
                'three_days_unfit' => 'sometimes|integer|min:0',
                'four_days_unfit' => 'sometimes|integer|min:0',
                'five_or_more_days_unfit' => 'sometimes|integer|min:0',
                'occupational_disease_cases' => 'sometimes|integer|min:0|max:127',
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
        $data = WorkAccidentsByProvince::all();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = WorkAccidentsByProvince::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Province ID'ye göre verileri listele.
     */
    public function indexByProvinceId($provinceId)
    {
        $data = WorkAccidentsByProvince::where('province_id', $provinceId)->get();
        return response()->json($data);
    }

    /**
     * Province code'a göre verileri listele.
     */
    public function indexByProvinceCode($provinceCode)
    {
        $data = WorkAccidentsByProvince::whereHas('province', function ($query) use ($provinceCode) {
            $query->where('province_code', $provinceCode);
        })->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validation kısmını geçtikten sonra
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        // Veriyi oluştur
        $workAccident = new WorkAccidentsByProvince();
        $workAccident->year                      = $request->year;
        $workAccident->province_id               = $request->province_id; // Province ID
        $workAccident->gender                    = $request->gender;
        $workAccident->works_on_accident_day     = $request->works_on_accident_day;
        $workAccident->unfit_on_accident_day     = $request->unfit_on_accident_day;
        $workAccident->two_days_unfit            = $request->two_days_unfit;
        $workAccident->three_days_unfit          = $request->three_days_unfit;
        $workAccident->four_days_unfit           = $request->four_days_unfit;
        $workAccident->five_or_more_days_unfit   = $request->five_or_more_days_unfit;
        $workAccident->occupational_disease_cases = $request->occupational_disease_cases;

        $result = $workAccident->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $workAccident]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri kaydedilirken hata oluştu.']);
        }
    }

    public function update(Request $request, $id)
    {
        // Validation kısmını geçtikten sonra
        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        // Veriyi bul ve güncelle
        $workAccident = WorkAccidentsByProvince::findOrFail($id);

        $workAccident->year                      = $request->year ?? $workAccident->year;
        $workAccident->province_id               = $request->province_id ?? $workAccident->province_id;
        $workAccident->gender                    = $request->gender ?? $workAccident->gender;
        $workAccident->works_on_accident_day     = $request->works_on_accident_day ?? $workAccident->works_on_accident_day;
        $workAccident->unfit_on_accident_day     = $request->unfit_on_accident_day ?? $workAccident->unfit_on_accident_day;
        $workAccident->two_days_unfit            = $request->two_days_unfit ?? $workAccident->two_days_unfit;
        $workAccident->three_days_unfit          = $request->three_days_unfit ?? $workAccident->three_days_unfit;
        $workAccident->four_days_unfit           = $request->four_days_unfit ?? $workAccident->four_days_unfit;
        $workAccident->five_or_more_days_unfit   = $request->five_or_more_days_unfit ?? $workAccident->five_or_more_days_unfit;
        $workAccident->occupational_disease_cases = $request->occupational_disease_cases ?? $workAccident->occupational_disease_cases;

        $result = $workAccident->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $workAccident]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.']);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkAccidentsByProvince $workAccidentsByProvince)
    {
        //
    }
}
