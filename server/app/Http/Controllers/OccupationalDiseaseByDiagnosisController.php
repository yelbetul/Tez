<?php

namespace App\Http\Controllers;

use App\Models\OccupationalDiseaseByDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

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
                'year'           => 'required|string|max:4',
                'diagnosis_code' => 'required|integer|exists:diagnosis_groups,id',
                'gender'         => 'required|boolean',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'           => 'sometimes|string|max:4',
                'diagnosis_code' => 'sometimes|integer|exists:diagnosis_groups,id',
                'gender'         => 'sometimes|boolean',
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
     * index: Tüm kayıtları, ilişkili occupationGroup bilgileriyle birlikte listele.
     */
    public function index()
    {
        $records = OccupationalDiseaseByDiagnosis::with('occupationGroup')->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * indexByYear: Belirtilen yıla göre kayıtları filtrele.
     */
    public function indexByYear($year)
    {
        $records = OccupationalDiseaseByDiagnosis::where('year', $year)
            ->with('occupationGroup')
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
            ->with('occupationGroup')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * indexByGroupCode: occupationGroup ilişkisi üzerinden group_code'a göre filtrele.
     */
    public function indexByGroupCode($groupCode)
    {
        $records = OccupationalDiseaseByDiagnosis::whereHas('occupationGroup', function ($query) use ($groupCode) {
                $query->where('group_code', $groupCode);
            })
            ->with('occupationGroup')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $records
        ]);
    }

    /**
     * indexBySubGroupCode: occupationGroup ilişkisi üzerinden sub_group_code'a göre filtrele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $records = OccupationalDiseaseByDiagnosis::whereHas('occupationGroup', function ($query) use ($subGroupCode) {
                $query->where('sub_group_code', $subGroupCode);
            })
            ->with('occupationGroup')
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
}
