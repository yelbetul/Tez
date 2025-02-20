<?php

namespace App\Http\Controllers;

use App\Models\FatalWorkAccidentsBySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FatalWorkAccidentsBySectorController extends Controller
{

    /**
     * Tüm verileri listele.
     */
    public function index()
    {
        $data = FatalWorkAccidentsBySector::all();
        return response()->json($data);
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
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        return null;
    }
}
