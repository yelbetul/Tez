<?php

namespace App\Http\Controllers;

use App\Models\WorkAccidentsByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\WorkAccidentsByProvinceImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperWorkAccidentByProvince;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;
class WorkAccidentsByProvinceController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

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
                'occupational_disease_cases' => 'required|integer|min:0',
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
                'occupational_disease_cases' => 'sometimes|integer|min:0',
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
        $data = WorkAccidentsByProvince::with('province')->get();
        return response()->json($data);
    }
    public function indexUser(Request $request)
    {
        // 1. Veri çekme sorgusu
        $query = DB::table('work_accidents_by_provinces')
            ->join('province_codes', 'work_accidents_by_provinces.province_id', '=', 'province_codes.id')
            ->select(
                'work_accidents_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name',
                DB::raw('CAST(SUM(works_on_accident_day) AS UNSIGNED) as works_on_accident_day'),
                DB::raw('CAST(SUM(unfit_on_accident_day) AS UNSIGNED) as unfit_on_accident_day'),
                DB::raw('CAST(SUM(two_days_unfit) AS UNSIGNED) as two_days_unfit'),
                DB::raw('CAST(SUM(three_days_unfit) AS UNSIGNED) as three_days_unfit'),
                DB::raw('CAST(SUM(four_days_unfit) AS UNSIGNED) as four_days_unfit'),
                DB::raw('CAST(SUM(five_or_more_days_unfit) AS UNSIGNED) as five_or_more_days_unfit'),
                DB::raw('CAST(SUM(occupational_disease_cases) AS UNSIGNED) as occupational_disease_cases'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (works_on_accident_day + unfit_on_accident_day) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (works_on_accident_day + unfit_on_accident_day) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'work_accidents_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name'
            );

        // Filtreleme parametreleri
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('work_accidents_by_provinces.year', $request->year);
        }

        if ($request->has('province_code') && $request->province_code !== 'all') {
            $query->where('province_codes.province_code', $request->province_code);
        }

        $data = $query->get();

        // 2. Veri özetini oluşturma
        $summary = [
            'total_accidents' => $data->sum(function($item) {
                return $item->works_on_accident_day + $item->unfit_on_accident_day 
                    + $item->two_days_unfit + $item->three_days_unfit 
                    + $item->four_days_unfit + $item->five_or_more_days_unfit;
            }),
            'total_unfit' => $data->sum(function($item) {
                return $item->unfit_on_accident_day + $item->two_days_unfit 
                    + $item->three_days_unfit + $item->four_days_unfit 
                    + $item->five_or_more_days_unfit;
            }),
            'total_diseases' => $data->sum('occupational_disease_cases'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        $prompt = AnalysisHelperWorkAccidentByProvince::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperWorkAccidentByProvince::getAICommentary($prompt);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'analysis' => $analysis
        ]);
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
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

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
        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

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
    public function destroy($id)
    {
        $workAccident = WorkAccidentsByProvince::find($id);
        if (!$workAccident) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $result = $workAccident->delete();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Kayıt başarıyla silindi.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt silinirken hata oluştu.'
            ]);
        }
    }
  
  	public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new WorkAccidentsByProvinceImport;
			Excel::import($import, $request->file('file'));

            if (!empty($import->failures())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bazı satırlar hatalı!',
                    'failures' => $import->failures(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Veriler başarıyla yüklendi.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu',
                'error' => $e->getMessage()
            ]);
        }
    }
}
