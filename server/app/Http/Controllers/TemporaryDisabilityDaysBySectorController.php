<?php

namespace App\Http\Controllers;

use App\Models\TemporaryDisabilityDaysBySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\TemporaryDisabilityDaysBySectorImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use App\Helpers\AnalysisHelperTemporaryDisability;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;
class TemporaryDisabilityDaysBySectorController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        // Store ve update için kurallar
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'group_id' => 'required|exists:sector_codes,id',
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
                'group_id' => 'sometimes|exists:sector_codes,id',
                'gender' => 'sometimes|boolean',
                'is_outpatient' => 'sometimes|boolean',
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
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }

    /**
     * Tüm verileri listele.
     */
    public function index()
    {
        $data = TemporaryDisabilityDaysBySector::with('sector')->get();
        return response()->json($data);
    }

    public function indexUser(Request $request)
    {
        // Veri çekme sorgusu
        $query = DB::table('temporary_disability_days_by_sectors')
            ->join('sector_codes', 'temporary_disability_days_by_sectors.group_id', '=', 'sector_codes.id')
            ->select(
                'temporary_disability_days_by_sectors.year',
                'sector_codes.sector_code',
                'sector_codes.group_code',
                'sector_codes.group_name',
                'sector_codes.sub_group_code',
                'sector_codes.sub_group_name',
                'sector_codes.pure_code',
                'sector_codes.pure_name',
                DB::raw('CAST(SUM(one_day_unfit) AS UNSIGNED) as one_day_unfit'),
                DB::raw('CAST(SUM(two_days_unfit) AS UNSIGNED) as two_days_unfit'),
                DB::raw('CAST(SUM(three_days_unfit) AS UNSIGNED) as three_days_unfit'),
                DB::raw('CAST(SUM(four_days_unfit) AS UNSIGNED) as four_days_unfit'),
                DB::raw('CAST(SUM(five_or_more_days_unfit) AS UNSIGNED) as five_or_more_days_unfit'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (one_day_unfit + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (one_day_unfit + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as female_count'),
                DB::raw('CAST(SUM(CASE WHEN is_outpatient = 1 THEN (one_day_unfit + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as outpatient_count'),
                DB::raw('CAST(SUM(CASE WHEN is_outpatient = 0 THEN (one_day_unfit + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as inpatient_count')
            )
            ->groupBy(
                'temporary_disability_days_by_sectors.year',
                'sector_codes.sector_code',
                'sector_codes.group_code',
                'sector_codes.group_name',
                'sector_codes.sub_group_code',
                'sector_codes.sub_group_name',
                'sector_codes.pure_code',
                'sector_codes.pure_name'
            );

        // Filtreleme parametreleri
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('temporary_disability_days_by_sectors.year', $request->year);
        }

        if ($request->has('sector_code') && $request->sector_code !== 'all') {
            $query->where('sector_codes.sector_code', $request->sector_code);
        }

        $data = $query->get();

        // Veri özetini oluşturma
        $summary = [
            'total_cases' => $data->sum(function($item) {
                return $item->one_day_unfit + $item->two_days_unfit + $item->three_days_unfit + 
                    $item->four_days_unfit + $item->five_or_more_days_unfit;
            }),
            'one_day_cases' => $data->sum('one_day_unfit'),
            'two_days_cases' => $data->sum('two_days_unfit'),
            'three_days_cases' => $data->sum('three_days_unfit'),
            'four_days_cases' => $data->sum('four_days_unfit'),
            'five_or_more_days_cases' => $data->sum('five_or_more_days_unfit'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count'),
            'outpatient_count' => $data->sum('outpatient_count'),
            'inpatient_count' => $data->sum('inpatient_count')
        ];

        // Cinsiyet dağılımı yüzdeleri
        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // Tedavi türü dağılımı
        $totalTreatment = $summary['outpatient_count'] + $summary['inpatient_count'];
        $summary['outpatient_percentage'] = $totalTreatment > 0 ? round(($summary['outpatient_count'] / $totalTreatment) * 100, 2) : 0;
        $summary['inpatient_percentage'] = $totalTreatment > 0 ? round(($summary['inpatient_count'] / $totalTreatment) * 100, 2) : 0;

        // AI analizi için prompt oluşturma
        $prompt = AnalysisHelperTemporaryDisability::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperTemporaryDisability::getAICommentary($prompt);

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
        $data = TemporaryDisabilityDaysBySector::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = TemporaryDisabilityDaysBySector::where('group_id', $groupId)->get();
        return response()->json($data);
    }

    /**
     * Sector code'a göre verileri listele.
     */
    public function indexBySectorCode($sectorCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($sectorCode) {
            $query->where('sector_code', $sectorCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Group code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Sub-group code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Pure code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($pureCode) {
            $query->where('pure_code', $pureCode);
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

        $temporaryDisability = new TemporaryDisabilityDaysBySector();
        $temporaryDisability->year                      = $request->year;
        $temporaryDisability->group_id                  = $request->group_id;
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

        $temporaryDisability = TemporaryDisabilityDaysBySector::findOrFail($id);

        $temporaryDisability->year                      = $request->year ?? $temporaryDisability->year;
        $temporaryDisability->group_id                  = $request->group_id ?? $temporaryDisability->group_id;
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
        $temporaryDisability = TemporaryDisabilityDaysBySector::find($id);

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
            $import = new TemporaryDisabilityDaysBySectorImport;
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
