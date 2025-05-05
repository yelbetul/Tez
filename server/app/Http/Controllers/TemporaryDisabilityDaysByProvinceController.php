<?php

namespace App\Http\Controllers;

use App\Models\TemporaryDisabilityDaysByProvince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\TemporaryDisabilityDaysByProvinceImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperTemporaryDisabilityByProvince;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;
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
                'one_day_unfit' => 'sometimes|integer|min:0',
                'two_days_unfit' => 'sometimes|integer|min:0',
                'three_days_unfit' => 'sometimes|integer|min:0',
                'four_days_unfit' => 'sometimes|integer|min:0',
                'five_or_more_days_unfit' => 'sometimes|integer|min:0',
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
        $data = TemporaryDisabilityDaysByProvince::with('province')->get();
        return response()->json($data);
    }
    public function indexUser(Request $request)
    {
        // 1. Data query
        $query = DB::table('temporary_disability_days_by_provinces')
            ->join('province_codes', 'temporary_disability_days_by_provinces.province_id', '=', 'province_codes.id')
            ->select(
                'temporary_disability_days_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name',
                'temporary_disability_days_by_provinces.is_outpatient',
                DB::raw('CAST(SUM(one_day_unfit) AS UNSIGNED) as one_day_unfit'),
                DB::raw('CAST(SUM(two_days_unfit) AS UNSIGNED) as two_days_unfit'),
                DB::raw('CAST(SUM(three_days_unfit) AS UNSIGNED) as three_days_unfit'),
                DB::raw('CAST(SUM(four_days_unfit) AS UNSIGNED) as four_days_unfit'),
                DB::raw('CAST(SUM(five_or_more_days_unfit) AS UNSIGNED) as five_or_more_days_unfit'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (one_day_unfit + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (one_day_unfit + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'temporary_disability_days_by_provinces.year',
                'province_codes.province_code',
                'province_codes.province_name',
                'temporary_disability_days_by_provinces.is_outpatient'
            );

        // Filter parameters
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('temporary_disability_days_by_provinces.year', $request->year);
        }

        if ($request->has('province_code') && $request->province_code !== 'all') {
            $query->where('province_codes.province_code', $request->province_code);
        }

        if ($request->has('is_outpatient') && $request->is_outpatient !== 'all') {
            $query->where('temporary_disability_days_by_provinces.is_outpatient', $request->is_outpatient);
        }

        $data = $query->get();

        // 2. Create data summary
        $summary = [
            'total_cases' => $data->sum(function($item) {
                return $item->one_day_unfit + $item->two_days_unfit 
                    + $item->three_days_unfit + $item->four_days_unfit 
                    + $item->five_or_more_days_unfit;
            }),
            'total_days_unfit' => $data->sum(function($item) {
                return ($item->one_day_unfit * 1) 
                    + ($item->two_days_unfit * 2) 
                    + ($item->three_days_unfit * 3) 
                    + ($item->four_days_unfit * 4) 
                    + ($item->five_or_more_days_unfit * 5); // Assuming minimum 5 days for this category
            }),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count'),
            'outpatient_cases' => $data->where('is_outpatient', true)->sum(function($item) {
                return $item->one_day_unfit + $item->two_days_unfit 
                    + $item->three_days_unfit + $item->four_days_unfit 
                    + $item->five_or_more_days_unfit;
            }),
            'inpatient_cases' => $data->where('is_outpatient', false)->sum(function($item) {
                return $item->one_day_unfit + $item->two_days_unfit 
                    + $item->three_days_unfit + $item->four_days_unfit 
                    + $item->five_or_more_days_unfit;
            })
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // 3. Generate AI analysis
        $prompt = AnalysisHelperTemporaryDisabilityByProvince::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperTemporaryDisabilityByProvince::getAICommentary($prompt);

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
  	
  
  	public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new TemporaryDisabilityDaysByProvinceImport;
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
