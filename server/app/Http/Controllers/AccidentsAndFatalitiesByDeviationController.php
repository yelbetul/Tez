<?php

namespace App\Http\Controllers;

use App\Models\AccidentsAndFatalitiesByDeviation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\AccidentsAndFatalitiesByDeviationImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperAccidentsByDeviations;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;

class AccidentsAndFatalitiesByDeviationController extends Controller
{
    /**
     * Validasyon
     */
    public function validateRequest($request, $type = 'store')
    {
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year'                     => 'required|string|max:4',
                'group_id'                 => 'required|exists:deviations,id',
                'gender'                   => 'required|boolean',
                'works_on_accident_day'     => 'required|integer|min:0',
                'unfit_on_accident_day'     => 'required|integer|min:0',
                'two_days_unfit'           => 'required|integer|min:0',
                'three_days_unfit'         => 'required|integer|min:0',
                'four_days_unfit'          => 'required|integer|min:0',
                'five_or_more_days_unfit'  => 'required|integer|min:0',
                'fatalities'               => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                     => 'sometimes|string|max:4',
                'group_id'                 => 'sometimes|exists:deviations,id',
                'gender'                   => 'sometimes|boolean',
                'works_on_accident_day'     => 'sometimes|integer|min:0',
                'unfit_on_accident_day'     => 'sometimes|integer|min:0',
                'two_days_unfit'           => 'sometimes|integer|min:0',
                'three_days_unfit'         => 'sometimes|integer|min:0',
                'four_days_unfit'          => 'sometimes|integer|min:0',
                'five_or_more_days_unfit'  => 'sometimes|integer|min:0',
                'fatalities'               => 'sometimes|integer|min:0',
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
        $data = AccidentsAndFatalitiesByDeviation::with('deviation')->get();
        return response()->json($data);
    }

    public function indexUser(Request $request)
    {
        $query = DB::table('accidents_and_fatalities_by_deviations')
            ->join('deviations', 'accidents_and_fatalities_by_deviations.group_id', '=', 'deviations.id')
            ->select(
                'accidents_and_fatalities_by_deviations.year',
                'deviations.group_code',
                'deviations.group_name',
                'deviations.sub_group_code',
                'deviations.sub_group_name',
                'deviations.deviation_code',
                DB::raw('CAST(SUM(works_on_accident_day) AS UNSIGNED) as works_on_accident_day'),
                DB::raw('CAST(SUM(unfit_on_accident_day) AS UNSIGNED) as unfit_on_accident_day'),
                DB::raw('CAST(SUM(two_days_unfit) AS UNSIGNED) as two_days_unfit'),
                DB::raw('CAST(SUM(three_days_unfit) AS UNSIGNED) as three_days_unfit'),
                DB::raw('CAST(SUM(four_days_unfit) AS UNSIGNED) as four_days_unfit'),
                DB::raw('CAST(SUM(five_or_more_days_unfit) AS UNSIGNED) as five_or_more_days_unfit'),
                DB::raw('CAST(SUM(fatalities) AS UNSIGNED) as fatalities'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (works_on_accident_day + unfit_on_accident_day + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit + fatalities) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (works_on_accident_day + unfit_on_accident_day + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit + fatalities) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'accidents_and_fatalities_by_deviations.year',
                'deviations.group_code',
                'deviations.group_name',
                'deviations.sub_group_code',
                'deviations.sub_group_name',
                'deviations.deviation_code'
            );

        // Filtreleme
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('accidents_and_fatalities_by_deviations.year', $request->year);
        }

        if ($request->has('group_code') && $request->group_code !== 'all') {
            $query->where('deviations.group_code', $request->group_code);
        }

        if ($request->has('sub_group_code') && $request->sub_group_code !== 'all') {
            $query->where('deviations.sub_group_code', $request->sub_group_code);
        }

        if ($request->has('deviation_code') && $request->deviation_code !== 'all') {
            $query->where('deviations.deviation_code', $request->deviation_code);
        }

        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('accidents_and_fatalities_by_deviations.gender', $request->gender);
        }

        $data = $query->get();

        // Özet veriler
        $summary = [
            'total_cases' => $data->sum(function($item) {
                return $item->works_on_accident_day + $item->unfit_on_accident_day
                    + $item->two_days_unfit + $item->three_days_unfit
                    + $item->four_days_unfit + $item->five_or_more_days_unfit;
            }),
            'total_unfit' => $data->sum(function($item) {
                return $item->unfit_on_accident_day + $item->two_days_unfit
                    + $item->three_days_unfit + $item->four_days_unfit
                    + $item->five_or_more_days_unfit;
            }),
            'total_fatalities' => $data->sum('fatalities'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // AI analiz promptu oluştur
        $prompt = AnalysisHelperAccidentsByDeviations::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperAccidentsByDeviations::getAICommentary($prompt);

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
        $data = AccidentsAndFatalitiesByDeviation::where('year', $year)->with('deviation')->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = AccidentsAndFatalitiesByDeviation::where('group_id', $groupId)->with('deviation')->get();
        return response()->json($data);
    }

    /**
     * Cinsiyete göre verileri listele.
     */
    public function indexByGender($gender)
    {
        $data = AccidentsAndFatalitiesByDeviation::where('gender', $gender)->with('deviation')->get();
        return response()->json($data);
    }

    /**
     * Injury Code'a göre verileri listele.
     */
    public function indexByInjuryCode($injuryCode)
    {
        $data = AccidentsAndFatalitiesByDeviation::whereHas('deviation', function ($query) use ($injuryCode) {
            $query->where('injury_code', $injuryCode);
        })->with('deviation')->get();

        return response()->json($data);
    }

    /**
     * Group Code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = AccidentsAndFatalitiesByDeviation::whereHas('deviation', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->with('deviation')->get();

        return response()->json($data);
    }

    /**
     * Sub Group Code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = AccidentsAndFatalitiesByDeviation::whereHas('deviation', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->with('deviation')->get();

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

        $record = new AccidentsAndFatalitiesByDeviation();
        $record->year = $request->year;
        $record->group_id = $request->group_id;
        $record->gender = $request->gender;
        $record->works_on_accident_day = $request->works_on_accident_day;
        $record->unfit_on_accident_day = $request->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit;
        $record->fatalities = $request->fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $record]);
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

        $record = AccidentsAndFatalitiesByDeviation::find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->year = $request->year ?? $record->year;
        $record->group_id = $request->group_id ?? $record->group_id;
        $record->gender = $request->gender ?? $record->gender;
        $record->works_on_accident_day = $request->works_on_accident_day ?? $record->works_on_accident_day;
        $record->unfit_on_accident_day = $request->unfit_on_accident_day ?? $record->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit ?? $record->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit ?? $record->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit ?? $record->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit ?? $record->five_or_more_days_unfit;
        $record->fatalities = $request->fatalities ?? $record->fatalities;

        $result = $record->save();

        return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $record]);
    }

    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $record = AccidentsAndFatalitiesByDeviation::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->delete();

        return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new AccidentsAndFatalitiesByDeviationImport;
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
