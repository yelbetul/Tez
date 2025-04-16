<?php

namespace App\Http\Controllers;

use App\Models\WorkAccidentsByAge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\WorkAccidentsByAgeImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class WorkAccidentsByAgeController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'year'                         => 'required|string|max:4',
                'age_id'                       => 'required|integer|exists:age_codes,id',
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
                'age_id'                       => 'sometimes|integer|exists:age_codes,id',
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
     * index: Tüm iş kazası yaşlarına göre kayıtlarını listele.
     */
    public function index()
    {
        $records = WorkAccidentsByAge::with('age')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    public function indexByYear($year)
    {
        $data = WorkAccidentsByAge::where('year', $year)->get();
        return response()->json($data);
    }
    /**
     * Age ID'ye göre verileri listele.
     */
    public function indexByAge($ageId)
    {
        $data = WorkAccidentsByAge::where('age_id', $ageId)->with('age')->get();
        return response()->json($data);
    }

    /**
     * Yeni iş kazası yaşlarına göre kayıt oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $record = new WorkAccidentsByAge();
        $record->year                       = $request->year;
        $record->age_id                     = $request->age_id;
        $record->gender                     = $request->gender;
        $record->works_on_accident_day      = $request->works_on_accident_day;
        $record->unfit_on_accident_day      = $request->unfit_on_accident_day;
        $record->two_days_unfit             = $request->two_days_unfit;
        $record->three_days_unfit           = $request->three_days_unfit;
        $record->four_days_unfit            = $request->four_days_unfit;
        $record->five_or_more_days_unfit    = $request->five_or_more_days_unfit;
        $record->occupational_disease_cases = $request->occupational_disease_cases;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş kazası yaşlarına göre kayıt başarıyla oluşturuldu.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası yaşlarına göre kayıt oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Varolan iş kazası yaşlarına göre kaydı güncelle.
     */
    public function update(Request $request, $id)
    {
        $record = WorkAccidentsByAge::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası yaşlarına göre kayıt bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $record->year                       = $request->year ?? $record->year;
        $record->age_id                     = $request->age_id ?? $record->age_id;
        $record->gender                     = $request->gender ?? $record->gender;
        $record->works_on_accident_day      = $request->works_on_accident_day ?? $record->works_on_accident_day;
        $record->unfit_on_accident_day      = $request->unfit_on_accident_day ?? $record->unfit_on_accident_day;
        $record->two_days_unfit             = $request->two_days_unfit ?? $record->two_days_unfit;
        $record->three_days_unfit           = $request->three_days_unfit ?? $record->three_days_unfit;
        $record->four_days_unfit            = $request->four_days_unfit ?? $record->four_days_unfit;
        $record->five_or_more_days_unfit    = $request->five_or_more_days_unfit ?? $record->five_or_more_days_unfit;
        $record->occupational_disease_cases = $request->occupational_disease_cases ?? $record->occupational_disease_cases;

        $result = $record->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş kazası yaşlarına göre kayıt başarıyla güncellendi.',
                'data'    => $record
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası yaşlarına göre kayıt güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * İlgili iş kazası yaşlarına göre kaydı sil.
     */
    public function destroy($id)
    {
        $record = WorkAccidentsByAge::find($id);
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'İş kazası yaşlarına göre kayıt bulunamadı!'
            ]);
        }

        $record->delete();
        return response()->json([
            'success' => true,
            'message' => 'İş kazası yaşlarına göre kayıt başarıyla silindi.'
        ]);
    }
  
  
  	public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new WorkAccidentsByAgeImport;
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
