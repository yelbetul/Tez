<?php

namespace App\Http\Controllers;

use App\Models\WorkAccidentsByMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\WorkAccidentsByMonthImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class WorkAccidentsByMonthController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'year'                         => 'required|string|max:4',
                'month_id'                     => 'required|integer|exists:months,id',
                'gender'                       => 'required|boolean',
                'works_on_accident_day'        => 'required|integer',
                'unfit_on_accident_day'        => 'required|integer',
                'two_days_unfit'               => 'required|integer',
                'three_days_unfit'             => 'required|integer',
                'four_days_unfit'              => 'required|integer',
                'five_or_more_days_unfit'      => 'required|integer',
                'occupational_disease_cases'   => 'required|integer',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                         => 'sometimes|string|max:4',
                'month_id'                     => 'sometimes|integer|exists:months,id',
                'gender'                       => 'sometimes|boolean',
                'works_on_accident_day'        => 'sometimes|integer',
                'unfit_on_accident_day'        => 'sometimes|integer',
                'two_days_unfit'               => 'sometimes|integer',
                'three_days_unfit'             => 'sometimes|integer',
                'four_days_unfit'              => 'sometimes|integer',
                'five_or_more_days_unfit'      => 'sometimes|integer',
                'occupational_disease_cases'   => 'sometimes|integer',
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
     * Tüm iş kazası kayıtlarını listele.
     */
    public function index()
    {
        $records = WorkAccidentsByMonth::with('month')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Yıla göre iş kazalarını listele.
     */
    public function indexByYear($year)
    {
        $records = WorkAccidentsByMonth::where('year', $year)
            ->with('month')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Cinsiyete göre iş kazalarını listele.
     */
    public function indexByGender($gender)
    {
        $records = WorkAccidentsByMonth::where('gender', $gender)
            ->with('month')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Ay ID'sine göre iş kazası kayıtlarını listele.
     */
    public function indexByMonth($monthId)
    {
        $records = WorkAccidentsByMonth::where('month_id', $monthId)
            ->with('month')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * Yeni iş kazası kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $record = new WorkAccidentsByMonth();
        $record->year = $request->year;
        $record->month_id = $request->month_id;
        $record->gender = $request->gender;
        $record->works_on_accident_day = $request->works_on_accident_day;
        $record->unfit_on_accident_day = $request->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit;
        $record->occupational_disease_cases = $request->occupational_disease_cases;

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
     * İş kazası kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $record = WorkAccidentsByMonth::find($id);
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
     * İş kazası kaydını sil.
     */
    public function destroy($id)
    {
        $record = WorkAccidentsByMonth::find($id);
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
            $import = new WorkAccidentsByMonthImport;
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
