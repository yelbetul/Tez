<?php

namespace App\Http\Controllers;

use App\Models\OccDiseaseFatalitiesByEmployerDuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\OccDiseaseFatalitiesByEmployerDurationImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class OccDiseaseFatalitiesByEmployerDurationController extends Controller
{
    /**
     * Validasyon Fonksiyonu
     */
    public function validateRequest($request, $type = 'store')
    {
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year'                     => 'required|string|max:4',
                'group_id'                 => 'required|exists:employee_employment_durations,id',
                'gender'                   => 'required|boolean',
                'occ_disease_cases'        => 'required|integer|min:0',
                'occ_disease_fatalities'   => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                     => 'sometimes|string|max:4',
                'group_id'                 => 'sometimes|exists:employee_employment_durations,id',
                'gender'                   => 'sometimes|boolean',
                'occ_disease_cases'        => 'sometimes|integer|min:0',
                'occ_disease_fatalities'   => 'sometimes|integer|min:0',
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
        $data = OccDiseaseFatalitiesByEmployerDuration::with('employeeEmploymentDuration')->get();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::where('year', $year)->with('employeeEmploymentDuration')->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::where('group_id', $groupId)->with('employeeEmploymentDuration')->get();
        return response()->json($data);
    }

    /**
     * Cinsiyete göre verileri listele.
     */
    public function indexByGender($gender)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::where('gender', $gender)->with('employeeEmploymentDuration')->get();
        return response()->json($data);
    }
    /**
     * Code'a göre verileri listele.
     */
    public function indexByCode($code)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::whereHas('employeeEmploymentDuration', function ($query) use ($code) {
            $query->where('code', $code);
        })->with('employeeEmploymentDuration')->get();

        return response()->json($data);
    }

    /**
     * Occupation Code'a göre verileri listele.
     */
    public function indexByOccupationCode($occupationCode)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::whereHas('employeeEmploymentDuration', function ($query) use ($occupationCode) {
            $query->where('occupation_code', $occupationCode);
        })->with('employeeEmploymentDuration')->get();

        return response()->json($data);
    }

    /**
     * Group Code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::whereHas('employeeEmploymentDuration', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->with('employeeEmploymentDuration')->get();

        return response()->json($data);
    }


    /**
     * Pure Code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::whereHas('employeeEmploymentDuration', function ($query) use ($pureCode) {
            $query->where('pure_code', $pureCode);
        })->with('employeeEmploymentDuration')->get();

        return response()->json($data);
    }

    /**
     * Sub Group Code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = OccDiseaseFatalitiesByEmployerDuration::whereHas('employeeEmploymentDuration', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->with('employeeEmploymentDuration')->get();

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

        $record = new OccDiseaseFatalitiesByEmployerDuration();
        $record->year = $request->year;
        $record->group_id = $request->group_id;
        $record->gender = $request->gender;
        $record->occ_disease_cases = $request->occ_disease_cases;
        $record->occ_disease_fatalities = $request->occ_disease_fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $record]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri kaydedilirken hata oluştu.']);
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

        $record = OccDiseaseFatalitiesByEmployerDuration::find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->year = $request->year ?? $record->year;
        $record->group_id = $request->group_id ?? $record->group_id;
        $record->gender = $request->gender ?? $record->gender;
        $record->occ_disease_cases = $request->occ_disease_cases ?? $record->occ_disease_cases;
        $record->occ_disease_fatalities = $request->occ_disease_fatalities ?? $record->occ_disease_fatalities;

        $result = $record->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $record]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.']);
        }
    }

    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $record = OccDiseaseFatalitiesByEmployerDuration::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->delete();

        return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            $import = new OccDiseaseFatalitiesByEmployerDurationImport;
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
