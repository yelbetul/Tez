<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\FatalWorkAccidentsByProvinceImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperFatalWorkAccidentsByProvince;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;
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
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }
    /**
     * Tüm verileri listele.
     */
    public function index()
    {
        $data = FatalWorkAccidentsByProvince::with('province')->get();
        return response()->json($data);
    }
    public function indexUser(Request $request)
    {
        // 1. Veri çekme sorgusu
        $query = DB::table('fatal_work_accidents_by_provinces')
            ->join('province_codes', 'fatal_work_accidents_by_provinces.province_id', '=', 'province_codes.id')
            ->select(
                'fatal_work_accidents_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name',
                DB::raw('CAST(SUM(work_accident_fatalities) AS UNSIGNED) as work_accident_fatalities'),
                DB::raw('CAST(SUM(occupational_disease_fatalities) AS UNSIGNED) as occupational_disease_fatalities'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (work_accident_fatalities + occupational_disease_fatalities) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (work_accident_fatalities + occupational_disease_fatalities) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'fatal_work_accidents_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name'
            );

        // Filtreleme parametreleri
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('fatal_work_accidents_by_provinces.year', $request->year);
        }

        if ($request->has('province_code') && $request->province_code !== 'all') {
            $query->where('province_codes.province_code', $request->province_code);
        }

        $data = $query->get();

        // 2. Veri özetini oluşturma
        $summary = [
            'total_fatalities' => $data->sum(function($item) {
                return $item->work_accident_fatalities + $item->occupational_disease_fatalities;
            }),
            'total_accident_fatalities' => $data->sum('work_accident_fatalities'),
            'total_disease_fatalities' => $data->sum('occupational_disease_fatalities'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        $prompt = AnalysisHelperFatalWorkAccidentsByProvince::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperFatalWorkAccidentsByProvince::getAICommentary($prompt);

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
  
  	public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new FatalWorkAccidentsByProvinceImport;
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
