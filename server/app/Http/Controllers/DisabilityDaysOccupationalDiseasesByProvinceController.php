<?php

namespace App\Http\Controllers;

use App\Models\DisabilityDaysOccupationalDiseasesByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\DisabilityDaysOccupationalDiseasesByProvinceImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperDisabilityOccupationalDiseaseByProvince;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;

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
    public function indexUser(Request $request)
    {
        // 1. Data query
        $query = DB::table('disability_days_occupational_diseases_by_provinces')
            ->join('province_codes', 'disability_days_occupational_diseases_by_provinces.province_id', '=', 'province_codes.id')
            ->select(
                'disability_days_occupational_diseases_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name',
                DB::raw('SUM(outpatient) as outpatient_cases'),
                DB::raw('SUM(inpatient) as inpatient_cases'),
                DB::raw('SUM(CASE WHEN gender = 0 THEN (outpatient + inpatient) ELSE 0 END) as male_count'),
                DB::raw('SUM(CASE WHEN gender = 1 THEN (outpatient + inpatient) ELSE 0 END) as female_count')
            )
            ->groupBy(
                'disability_days_occupational_diseases_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name'
            );

        // Filter parameters
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('disability_days_occupational_diseases_by_provinces.year', $request->year);
        }

        if ($request->has('province_code') && $request->province_code !== 'all') {
            $query->where('province_codes.province_code', $request->province_code);
        }

        $data = $query->get();

        // 2. Create data summary
        $summary = [
            'total_cases' => $data->sum(function($item) {
                return $item->outpatient_cases + $item->inpatient_cases;
            }),
            'outpatient_cases' => $data->sum('outpatient_cases'),
            'inpatient_cases' => $data->sum('inpatient_cases'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // 3. Generate AI analysis
        $prompt = AnalysisHelperDisabilityOccupationalDiseaseByProvince::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperDisabilityOccupationalDiseaseByProvince::getAICommentary($prompt);

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


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new DisabilityDaysOccupationalDiseasesByProvinceImport;
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
