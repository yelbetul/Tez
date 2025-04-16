<?php

namespace App\Http\Controllers;

use App\Models\TemporaryDisabilityDaysByMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\TemporaryDisabilityDaysByMonthImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


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
