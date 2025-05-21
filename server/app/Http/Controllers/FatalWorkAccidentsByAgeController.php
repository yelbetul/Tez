<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsByAge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\FatalWorkAccidentsByAgeImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperFatalWorkAccidentsByAge;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;
class FatalWorkAccidentsByAgeController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'year'                            => 'required|string|max:4',
                'age_id'                          => 'required|integer|exists:age_codes,id',
                'gender'                          => 'required|boolean',
                'work_accident_fatalities'        => 'required|integer',
                'occupational_disease_fatalities' => 'required|integer',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                            => 'sometimes|string|max:4',
                'age_id'                          => 'sometimes|integer|exists:age_codes,id',
                'gender'                          => 'sometimes|boolean',
                'work_accident_fatalities'        => 'sometimes|integer',
                'occupational_disease_fatalities' => 'sometimes|integer',
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
     * Tüm İş kazası kayıtlarını listele.
     */
    public function index()
    {
        $records = FatalWorkAccidentsByAge::with('age')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    public function indexUser(Request $request)
    {
        // 1. Data query with proper type casting
        $query = DB::table('fatal_work_accidents_by_ages')
            ->join('age_codes', 'fatal_work_accidents_by_ages.age_id', '=', 'age_codes.id')
            ->select(
                'fatal_work_accidents_by_ages.year',
                'age_codes.age',
                DB::raw('CAST(SUM(work_accident_fatalities) AS UNSIGNED) as work_accident_fatalities'),
                DB::raw('CAST(SUM(occupational_disease_fatalities) AS UNSIGNED) as occupational_disease_fatalities'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (work_accident_fatalities + occupational_disease_fatalities) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (work_accident_fatalities + occupational_disease_fatalities) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'fatal_work_accidents_by_ages.year',
                'age_codes.age'
            );

        // Filter parameters
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('fatal_work_accidents_by_ages.year', $request->year);
        }

        if ($request->has('age') && $request->age !== 'all') {
            $query->where('age_codes.age', $request->age);
        }

        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('fatal_work_accidents_by_ages.gender', $request->gender);
        }

        $data = $query->get();

        // 2. Create data summary
        $summary = [
            'total_fatalities' => $data->sum(function($item) {
                return $item->work_accident_fatalities + $item->occupational_disease_fatalities;
            }),
            'total_work_accident_fatalities' => $data->sum('work_accident_fatalities'),
            'total_occupational_disease_fatalities' => $data->sum('occupational_disease_fatalities'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // 3. Generate AI analysis
        $prompt = AnalysisHelperFatalWorkAccidentsByAge::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperFatalWorkAccidentsByAge::getAICommentary($prompt);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'analysis' => $analysis
        ]);
    }

    public function indexByYear($year)
    {
        $data = FatalWorkAccidentsByAge::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Route üzerinden gelen age_id parametresine göre kayıtları filtrele.
     */
    public function indexByAge($ageId)
    {
        $records = FatalWorkAccidentsByAge::where('age_id', $ageId)->with('age')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Yeni İş kazası kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $record = new FatalWorkAccidentsByAge();
        $record->year = $request->year;
        $record->age_id = $request->age_id;
        $record->gender = $request->gender;
        $record->work_accident_fatalities = $request->work_accident_fatalities;
        $record->occupational_disease_fatalities = $request->occupational_disease_fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş kazası kaydı başarıyla oluşturuldu.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Varolan İş kazası kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $record = FatalWorkAccidentsByAge::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $record->year = $request->year ?? $record->year;
        $record->age_id = $request->age_id ?? $record->age_id;
        $record->gender = $request->gender ?? $record->gender;
        $record->work_accident_fatalities = $request->work_accident_fatalities ?? $record->work_accident_fatalities;
        $record->occupational_disease_fatalities = $request->occupational_disease_fatalities ?? $record->occupational_disease_fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş kazası kaydı başarıyla güncellendi.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * İlgili İş kazası kaydını sil.
     */
    public function destroy($id)
    {
        $record = FatalWorkAccidentsByAge::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası kaydı bulunamadı!'
            ]);
        }

        $record->delete();
        return response()->json([
            'success' => true,
            'message' => 'İş kazası kaydı başarıyla silindi.'
        ]);
    }
  
  
  	public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new FatalWorkAccidentsByAgeImport;
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
