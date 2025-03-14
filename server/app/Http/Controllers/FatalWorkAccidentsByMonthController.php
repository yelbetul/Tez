<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsByMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class FatalWorkAccidentsByMonthController extends Controller
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
                'month_id'                        => 'required|integer|exists:months,id',
                'gender'                          => 'required|boolean',
                'work_accident_fatalities'        => 'required|integer',
                'occupational_disease_fatalities' => 'required|integer',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                            => 'sometimes|string|max:4',
                'month_id'                        => 'sometimes|integer|exists:months,id',
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
     * Tüm iş kazası kayıtlarını listele.
     */
    public function index()
    {
        $records = FatalWorkAccidentsByMonth::with('month')->get();
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
        $records = FatalWorkAccidentsByMonth::where('year', $year)
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
        $records = FatalWorkAccidentsByMonth::where('gender', $gender)
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
        $records = FatalWorkAccidentsByMonth::where('month_id', $monthId)
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

        $record = new FatalWorkAccidentsByMonth();
        $record->year = $request->year;
        $record->month_id = $request->month_id;
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
     * İş kazası kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $record = FatalWorkAccidentsByMonth::find($id);
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
     * İş kazası kaydını sil.
     */
    public function destroy($id)
    {
        $record = FatalWorkAccidentsByMonth::find($id);
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
}
