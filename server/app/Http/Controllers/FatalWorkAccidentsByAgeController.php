<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsByAge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Imports\FatalWorkAccidentsByAgeImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

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
