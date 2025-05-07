<?php

namespace App\Http\Controllers;

use App\Models\TemporaryDisabilityDaysByMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\TemporaryDisabilityDaysByMonthImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperTemporaryDisabilityDaysByMonth;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;

class TemporaryDisabilityDaysByMonthController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'year'                      => 'required|string|max:4',
                'month_id'                  => 'required|integer|exists:months,id',
                'gender'                    => 'required|boolean', 'works_on_accident_day'     => 'required|integer|min:0',
                'unfit_on_accident_day'     => 'required|integer|min:0','one_day_unfit'             => 'required|integer',
                'two_days_unfit'            => 'required|integer',
                'three_days_unfit'          => 'required|integer',
                'four_days_unfit'           => 'required|integer',
                'five_or_more_days_unfit'   => 'required|integer',
                'occupational_disease_cases'=> 'required|integer',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                      => 'sometimes|string|max:4',
                'month_id'                  => 'sometimes|integer|exists:months,id',
                'gender'                    => 'sometimes|boolean',
                'works_on_accident_day'     => 'sometimes|integer',
                'unfit_on_accident_day'     => 'sometimes|integer',
                'two_days_unfit'            => 'sometimes|integer',
                'three_days_unfit'          => 'sometimes|integer',
                'four_days_unfit'           => 'sometimes|integer',
                'five_or_more_days_unfit'   => 'sometimes|integer',
                'occupational_disease_cases'=> 'sometimes|integer',
            ];
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        return null;
    }

    /**
     * Tüm geçici iş göremezlik günü kayıtlarını listele.
     */
    public function index()
    {
        $records = TemporaryDisabilityDaysByMonth::with('month')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    public function indexUser(Request $request)
    {
        // 1. Data query with proper type casting
        $query = DB::table('temporary_disability_days_by_months')
            ->join('months', 'temporary_disability_days_by_months.month_id', '=', 'months.id')
            ->select(
                'temporary_disability_days_by_months.year',
                'months.month_name as month',
                DB::raw('CAST(SUM(works_on_accident_day) AS UNSIGNED) as works_on_accident_day'),
                DB::raw('CAST(SUM(unfit_on_accident_day) AS UNSIGNED) as unfit_on_accident_day'),
                DB::raw('CAST(SUM(two_days_unfit) AS UNSIGNED) as two_days_unfit'),
                DB::raw('CAST(SUM(three_days_unfit) AS UNSIGNED) as three_days_unfit'),
                DB::raw('CAST(SUM(four_days_unfit) AS UNSIGNED) as four_days_unfit'),
                DB::raw('CAST(SUM(five_or_more_days_unfit) AS UNSIGNED) as five_or_more_days_unfit'),
                DB::raw('CAST(SUM(occupational_disease_cases) AS UNSIGNED) as occupational_disease_cases'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (works_on_accident_day + unfit_on_accident_day + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (works_on_accident_day + unfit_on_accident_day + two_days_unfit + three_days_unfit + four_days_unfit + five_or_more_days_unfit) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'temporary_disability_days_by_months.year',
                'months.month_name'
            );

        // Filter parameters
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('temporary_disability_days_by_months.year', $request->year);
        }

        if ($request->has('month') && $request->month !== 'all') {
            $query->where('months.month_name', $request->month);
        }

        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('temporary_disability_days_by_months.gender', $request->gender);
        }

        $data = $query->get();

        // 2. Create data summary
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

        // 3. Generate AI analysis
        $prompt = AnalysisHelperTemporaryDisabilityDaysByMonth::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperTemporaryDisabilityDaysByMonth::getAICommentary($prompt);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'analysis' => $analysis
        ]);
    }

    /**
     * Yıla göre geçici iş göremezlik günü kayıtlarını listele.
     */
    public function indexByYear($year)
    {
        $records = TemporaryDisabilityDaysByMonth::where('year', $year)
            ->with('month')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Cinsiyete göre geçici iş göremezlik günü kayıtlarını listele.
     */
    public function indexByGender($gender)
    {
        $records = TemporaryDisabilityDaysByMonth::where('gender', $gender)
            ->with('month')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Ay ID'sine göre geçici iş göremezlik günü kayıtlarını listele.
     */
    public function indexByMonth($monthId)
    {
        $records = TemporaryDisabilityDaysByMonth::where('month_id', $monthId)
            ->with('month')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Yeni geçici iş göremezlik günü kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $record = new TemporaryDisabilityDaysByMonth();
        $record->year = $request->year;
        $record->month_id = $request->month_id;
        $record->gender = $request->gender;
        $record->works_on_accident_day     = $request->works_on_accident_day;
        $record->unfit_on_accident_day     = $request->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit;
        $record->occupational_disease_cases = $request->occupational_disease_cases;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Geçici iş göremezlik günü kaydı başarıyla oluşturuldu.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Geçici iş göremezlik günü kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Geçici iş göremezlik günü kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $record = TemporaryDisabilityDaysByMonth::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Geçici iş göremezlik günü kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $record->year = $request->year ?? $record->year;
        $record->month_id = $request->month_id ?? $record->month_id;
        $record->gender = $request->gender ?? $record->gender;
        $record->works_on_accident_day = $request->works_on_accident_day ?? $record->works_on_accident_day;
        $record->unfit_on_accident_day = $request->unfit_on_accident_day ?? $record->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit ?? $record->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit ?? $record->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit ?? $record->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit ?? $record->five_or_more_days_unfit;
        $record->occupational_disease_cases = $request->occupational_disease_cases ?? $record->occupational_disease_cases;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Geçici iş göremezlik günü kaydı başarıyla güncellendi.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Geçici iş göremezlik günü kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Geçici iş göremezlik günü kaydını sil.
     */
    public function destroy($id)
    {
        $record = TemporaryDisabilityDaysByMonth::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Geçici iş göremezlik günü kaydı bulunamadı!'
            ]);
        }

        $record->delete();
        return response()->json([
            'success' => true,
            'message' => 'Geçici iş göremezlik günü kaydı başarıyla silindi.'
        ]);
    }
  
  	public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new TemporaryDisabilityDaysByMonthImport;
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
