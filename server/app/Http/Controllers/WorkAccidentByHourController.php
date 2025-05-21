<?php

namespace App\Http\Controllers;

use App\Models\WorkAccidentByHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\WorkAccidentByHourImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperAccidentsByHours;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;


class WorkAccidentByHourController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'group_id' => 'required|exists:time_intervals,id',
                'gender' => 'required|boolean',
                'works_on_accident_day' => 'required|integer|min:0',
                'unfit_on_accident_day' => 'required|integer|min:0',
                'two_days_unfit' => 'required|integer|min:0',
                'three_days_unfit' => 'required|integer|min:0',
                'four_days_unfit' => 'required|integer|min:0',
                'five_or_more_days_unfit' => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'group_id' => 'sometimes|exists:time_intervals,id',
                'gender' => 'sometimes|boolean',
                'works_on_accident_day' => 'sometimes|integer|min:0',
                'unfit_on_accident_day' => 'sometimes|integer|min:0',
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
        $data = WorkAccidentByHour::with('timeInterval')->get();
        return response()->json($data);
    }
    public function indexUser(Request $request)
    {
        $query = DB::table('work_accident_by_hours')
            ->join('time_intervals', 'work_accident_by_hours.group_id', '=', 'time_intervals.id')
            ->select(
                'work_accident_by_hours.year',
                'time_intervals.code',
                'time_intervals.time_interval',
                DB::raw('CAST(SUM(works_on_accident_day) AS UNSIGNED) as works_on_accident_day'),
                DB::raw('CAST(SUM(unfit_on_accident_day) AS UNSIGNED) as unfit_on_accident_day'),
                DB::raw('CAST(SUM(two_days_unfit) AS UNSIGNED) as two_days_unfit'),
                DB::raw('CAST(SUM(three_days_unfit) AS UNSIGNED) as three_days_unfit'),
                DB::raw('CAST(SUM(four_days_unfit) AS UNSIGNED) as four_days_unfit'),
                DB::raw('CAST(SUM(five_or_more_days_unfit) AS UNSIGNED) as five_or_more_days_unfit'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (works_on_accident_day + unfit_on_accident_day + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (works_on_accident_day + unfit_on_accident_day + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'work_accident_by_hours.year',
                'time_intervals.code',
                'time_intervals.time_interval'
            );

        // Filtreleme
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('work_accident_by_hours.year', $request->year);
        }

        if ($request->has('time_interval') && $request->time_interval !== 'all') {
            $query->where('time_intervals.code', $request->time_interval);
        }

        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('work_accident_by_hours.gender', $request->gender);
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
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // AI analiz promptu oluştur
        $prompt = AnalysisHelperAccidentsByHours::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperAccidentsByHours::getAICommentary($prompt);

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
        $data = WorkAccidentByHour::where('year', $year)->get();
        return response()->json($data);
    }
    /**
     * Cinsiyete göre verileri listele.
     */
    public function indexByGender($gender)
    {
        $data = WorkAccidentByHour::where('gender', $gender)->get();
        return response()->json($data);
    }
    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = WorkAccidentByHour::where('group_id', $groupId)->get();
        return response()->json($data);
    }

    /**
     * Sector code'a göre verileri listele.
     */
    public function indexBySectorCode($sectorCode)
    {
        $data = WorkAccidentByHour::whereHas('timeInterval', function ($query) use ($sectorCode) {
            $query->where('sector_code', $sectorCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Group code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = WorkAccidentByHour::whereHas('timeInterval', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Sub-group code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = WorkAccidentByHour::whereHas('timeInterval', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Pure code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = WorkAccidentByHour::whereHas('timeInterval', function ($query) use ($pureCode) {
            $query->where('pure_code', $pureCode);
        })->get();
        return response()->json($data);
    }


    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $workAccident = new WorkAccidentByHour();
        $workAccident->year                      = $request->year;
        $workAccident->group_id                  = $request->group_id;
        $workAccident->gender                    = $request->gender;
        $workAccident->works_on_accident_day     = $request->works_on_accident_day;
        $workAccident->unfit_on_accident_day     = $request->unfit_on_accident_day;
        $workAccident->two_days_unfit            = $request->two_days_unfit;
        $workAccident->three_days_unfit          = $request->three_days_unfit;
        $workAccident->four_days_unfit           = $request->four_days_unfit;
        $workAccident->five_or_more_days_unfit   = $request->five_or_more_days_unfit;

        $result = $workAccident->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $workAccident]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri kaydedilirken hata oluştu.']);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $workAccident = WorkAccidentByHour::findOrFail($id);

        $workAccident->year                      = $request->year ?? $workAccident->year;
        $workAccident->group_id                  = $request->group_id ?? $workAccident->group_id;
        $workAccident->gender                    = $request->gender ?? $workAccident->gender;
        $workAccident->works_on_accident_day     = $request->works_on_accident_day ?? $workAccident->works_on_accident_day;
        $workAccident->unfit_on_accident_day     = $request->unfit_on_accident_day ?? $workAccident->unfit_on_accident_day;
        $workAccident->two_days_unfit            = $request->two_days_unfit ?? $workAccident->two_days_unfit;
        $workAccident->three_days_unfit          = $request->three_days_unfit ?? $workAccident->three_days_unfit;
        $workAccident->four_days_unfit           = $request->four_days_unfit ?? $workAccident->four_days_unfit;
        $workAccident->five_or_more_days_unfit   = $request->five_or_more_days_unfit ?? $workAccident->five_or_more_days_unfit;

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
        $workAccident = WorkAccidentByHour::find($id);

        if (!$workAccident) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $result = $workAccident->delete();

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
            $import = new WorkAccidentByHourImport;
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
