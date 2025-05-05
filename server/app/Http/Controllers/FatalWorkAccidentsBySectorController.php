<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsBySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\FatalWorkAccidentsBySectorImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Helpers\AnalysisHelperFatalWorkAccidents;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;

class FatalWorkAccidentsBySectorController extends Controller
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
                'group_id' => 'required|exists:sector_codes,id',
                'gender' => 'required|boolean',
                'work_accident_fatalities' => 'required|integer|min:0',
                'occupational_disease_fatalities' => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'group_id' => 'sometimes|exists:sector_codes,id',
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
        $data = FatalWorkAccidentsBySector::with('sector')->get();
        return response()->json($data);
    }

    public function indexUser(Request $request){
        // 1. Önce mevcut veri çekme işlemini yapın
        $query = DB::table('fatal_work_accidents_by_sectors')
            ->join('sector_codes', 'fatal_work_accidents_by_sectors.group_id', '=', 'sector_codes.id')
            ->select(
                'fatal_work_accidents_by_sectors.year',
                'sector_codes.sector_code',
                'sector_codes.group_code',
                'sector_codes.group_name',
                'sector_codes.sub_group_code',
                'sector_codes.sub_group_name',
                'sector_codes.pure_code',
                'sector_codes.pure_name',
                DB::raw('CAST(SUM(work_accident_fatalities) AS UNSIGNED) as work_accident_fatalities'),
                DB::raw('CAST(SUM(occupational_disease_fatalities) AS UNSIGNED) as occupational_disease_fatalities'),
                DB::raw('CAST(SUM(CASE WHEN gender = 0 THEN (work_accident_fatalities + occupational_disease_fatalities) ELSE 0 END) AS UNSIGNED) as male_count'),
                DB::raw('CAST(SUM(CASE WHEN gender = 1 THEN (work_accident_fatalities + occupational_disease_fatalities) ELSE 0 END) AS UNSIGNED) as female_count')
            )
            ->groupBy(
                'fatal_work_accidents_by_sectors.year',
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
            $query->where('fatal_work_accidents_by_sectors.year', $request->year);
        }

        if ($request->has('sector_code') && $request->sector_code !== 'all') {
            $query->where('sector_codes.sector_code', $request->sector_code);
        }

        $data = $query->get();

        // 2. Veri özetini oluşturun
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

        $prompt = AnalysisHelperFatalWorkAccidents::buildAIPrompt($request, $summary);
        $analysis = AnalysisHelperFatalWorkAccidents::getAICommentary($prompt);

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
        $data = FatalWorkAccidentsBySector::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = FatalWorkAccidentsBySector::where('group_id', $groupId)->get();
        return response()->json($data);
    }
    /**
     * Sector code'a göre verileri listele.
     */
    public function indexBySectorCode($sectorCode)
    {
        $data = FatalWorkAccidentsBySector::whereHas('sector', function ($query) use ($sectorCode) {
            $query->where('sector_code', $sectorCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Group code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = FatalWorkAccidentsBySector::whereHas('sector', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Sub-group code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = FatalWorkAccidentsBySector::whereHas('sector', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Pure code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = FatalWorkAccidentsBySector::whereHas('sector', function ($query) use ($pureCode) {
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

        $record = new FatalWorkAccidentsBySector();
        $record->year = $request->year;
        $record->group_id = $request->group_id;
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

        $record = FatalWorkAccidentsBySector::find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.'], 404);
        }

        $record->year = $request->year ?? $record->year;
        $record->group_id = $request->group_id ?? $record->group_id;
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
        $record = FatalWorkAccidentsBySector::find($id);

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
  
  
  	/**
     * İş kazası verilerini içe aktar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new FatalWorkAccidentsBySectorImport;
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
              	'message' => 'Veriler başarıyla yüklendi !'
            ]);

        } catch (\Exception $e) {
            Log::error('İçe aktarma hatası: ' . $e->getMessage());
            return back()->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }
  	
}
