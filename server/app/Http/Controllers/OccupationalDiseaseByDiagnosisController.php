<?php

namespace App\Http\Controllers;

use App\Models\OccupationalDiseaseByDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\OccupationalDiseaseByDiagnosisImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Helpers\AnalysisHelperOccupationalDisease;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;

class OccupationalDiseaseByDiagnosisController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'year'                       => 'required|string|max:4',
                'diagnosis_code'             => 'required|integer|exists:diagnosis_groups,id',
                'gender'                     => 'required|boolean',
                'occupational_disease_cases' => 'required|integer',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                       => 'sometimes|string|max:4',
                'diagnosis_code'             => 'sometimes|integer|exists:diagnosis_groups,id',
                'gender'                     => 'sometimes|boolean',
                'occupational_disease_cases' => 'sometimes|integer',
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
     * index: Tüm kayıtları, ilişkili diagnosisGroup bilgileriyle birlikte listele.
     */
    public function index()
    {
        $records = OccupationalDiseaseByDiagnosis::with('diagnosisGroup')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }


    public function indexUser(Request $request)
    {
        // 1. Data query with proper type casting
        $query = DB::table('occupational_disease_by_diagnoses')
            ->join('diagnosis_groups', 'occupational_disease_by_diagnoses.diagnosis_code', '=', 'diagnosis_groups.id')
            ->select(
                'occupational_disease_by_diagnoses.year',
                'diagnosis_groups.group_code',
                'diagnosis_groups.group_name',
                'diagnosis_groups.sub_group_code',
                'diagnosis_groups.sub_group_name',
                DB::raw('CAST(SUM(occupational_disease_cases) AS UNSIGNED) as occupational_disease_cases'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN occupational_disease_cases ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN occupational_disease_cases ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'occupational_disease_by_diagnoses.year',
                'diagnosis_groups.group_code',
                'diagnosis_groups.group_name',
                'diagnosis_groups.sub_group_code',
                'diagnosis_groups.sub_group_name'
            );

        // Filter parameters
        if ($request->has('year') && $request->year !== 'all') {
            $query->where('occupational_disease_by_diagnoses.year', $request->year);
        }

        if ($request->has('group_code') && $request->group_code !== 'all') {
            $query->where('diagnosis_groups.group_code', $request->group_code);
        }

        if ($request->has('sub_group_code') && $request->sub_group_code !== 'all') {
            $query->where('diagnosis_groups.sub_group_code', $request->sub_group_code);
        }

        if ($request->has('gender') && $request->gender !== 'all') {
            $query->where('occupational_disease_by_diagnoses.gender', $request->gender);
        }

        $data = $query->get();

        // 2. Create data summary
        $summary = [
            'total_disease_cases' => $data->sum('occupational_disease_cases'),
            'male_count' => $data->sum('male_count'),
            'female_count' => $data->sum('female_count')
        ];

        $totalGender = $summary['male_count'] + $summary['female_count'];
        $summary['male_percentage'] = $totalGender > 0 ? round(($summary['male_count'] / $totalGender) * 100, 2) : 0;
        $summary['female_percentage'] = $totalGender > 0 ? round(($summary['female_count'] / $totalGender) * 100, 2) : 0;

        // 3. Generate AI analysis (you'll need to update your AnalysisHelper for this data)
        $prompt = AnalysisHelperOccupationalDisease::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperOccupationalDisease::getAICommentary($prompt);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'analysis' => $analysis
        ]);
    }
    /**
     * indexByYear: Belirtilen yıla göre kayıtları filtrele.
     */
    public function indexByYear($year)
    {
        $records = OccupationalDiseaseByDiagnosis::where('year', $year)
            ->with('diagnosisGroup')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * indexByGender: Belirtilen cinsiyete göre kayıtları filtrele.
     */
    public function indexByGender($gender)
    {
        $records = OccupationalDiseaseByDiagnosis::where('gender', $gender)
            ->with('diagnosisGroup')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * indexByGroupCode: diagnosisGroup ilişkisi üzerinden group_code'a göre filtrele.
     */
    public function indexByGroupCode($groupCode)
    {
        $records = OccupationalDiseaseByDiagnosis::whereHas('diagnosisGroup', function ($query) use ($groupCode) {
                $query->where('group_code', $groupCode);
            })
            ->with('diagnosisGroup')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * indexBySubGroupCode: diagnosisGroup ilişkisi üzerinden sub_group_code'a göre filtrele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $records = OccupationalDiseaseByDiagnosis::whereHas('diagnosisGroup', function ($query) use ($subGroupCode) {
                $query->where('sub_group_code', $subGroupCode);
            })
            ->with('diagnosisGroup')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * store: Yeni kayıt oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $record = new OccupationalDiseaseByDiagnosis();
        $record->year = $request->year;
        $record->diagnosis_code = $request->diagnosis_code;
        $record->gender = $request->gender;
        $record->occupational_disease_cases = $request->occupational_disease_cases;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Kayıt başarıyla oluşturuldu.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * update: Varolan kaydı güncelle.
     */
    public function update(Request $request, $id)
    {
        $record = OccupationalDiseaseByDiagnosis::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $record->year = $request->year ?? $record->year;
        $record->diagnosis_code = $request->diagnosis_code ?? $record->diagnosis_code;
        $record->gender = $request->gender ?? $record->gender;
        $record->occupational_disease_cases = $request->occupational_disease_cases ?? $record->occupational_disease_cases;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Kayıt başarıyla güncellendi.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * destroy: Kaydı sil.
     */
    public function destroy($id)
    {
        $record = OccupationalDiseaseByDiagnosis::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Kayıt bulunamadı!'
            ]);
        }

        $record->delete();
        return response()->json([
            'success' => true,
            'message' => 'Kayıt başarıyla silindi.'
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new OccupationalDiseaseByDiagnosisImport;
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
