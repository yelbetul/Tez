<?php

namespace App\Http\Controllers;

use App\Models\TemporaryDisabilityDaysBySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class TemporaryDisabilityDaysBySectorController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        // Store ve update için kurallar
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'group_id' => 'required|exists:sector_codes,id',
                'gender' => 'required|boolean',
                'is_outpatient' => 'required|boolean',
                'is_inpatient' => 'required|boolean',
                'one_day_unfit' => 'required|integer|min:0',
                'two_days_unfit' => 'required|integer|min:0',
                'three_days_unfit' => 'required|integer|min:0',
                'four_days_unfit' => 'required|integer|min:0',
                'five_or_more_days_unfit' => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'group_id' => 'sometimes|exists:sector_codes,id',
                'gender' => 'sometimes|boolean',
                'is_outpatient' => 'sometimes|boolean',
                'is_inpatient' => 'sometimes|boolean',
                'one_day_unfit' => 'sometimes|integer|min:0',
                'two_days_unfit' => 'sometimes|integer|min:0',
                'three_days_unfit' => 'sometimes|integer|min:0',
                'four_days_unfit' => 'sometimes|integer|min:0',
                'five_or_more_days_unfit' => 'sometimes|integer|min:0',
            ];
        }

        // Validator oluştur
        $validator = Validator::make($request->all(), $rules);

        // Hata varsa döndür
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        return null;
    }

    /**
     * Tüm verileri listele.
     */
    public function index()
    {
        $data = TemporaryDisabilityDaysBySector::all();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = TemporaryDisabilityDaysBySector::where('year', $year)->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = TemporaryDisabilityDaysBySector::where('group_id', $groupId)->get();
        return response()->json($data);
    }

    /**
     * Sector code'a göre verileri listele.
     */
    public function indexBySectorCode($sectorCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($sectorCode) {
            $query->where('sector_code', $sectorCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Group code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Sub-group code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Pure code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = TemporaryDisabilityDaysBySector::whereHas('sector', function ($query) use ($pureCode) {
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

        $temporaryDisability = new TemporaryDisabilityDaysBySector();
        $temporaryDisability->year                      = $request->year;
        $temporaryDisability->group_id                  = $request->group_id;
        $temporaryDisability->gender                    = $request->gender;
        $temporaryDisability->is_outpatient             = $request->is_outpatient;
        $temporaryDisability->is_inpatient              = $request->is_inpatient;
        $temporaryDisability->one_day_unfit             = $request->one_day_unfit;
        $temporaryDisability->two_days_unfit            = $request->two_days_unfit;
        $temporaryDisability->three_days_unfit          = $request->three_days_unfit;
        $temporaryDisability->four_days_unfit           = $request->four_days_unfit;
        $temporaryDisability->five_or_more_days_unfit   = $request->five_or_more_days_unfit;

        $result = $temporaryDisability->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $temporaryDisability]);
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

        $temporaryDisability = TemporaryDisabilityDaysBySector::findOrFail($id);

        $temporaryDisability->year                      = $request->year ?? $temporaryDisability->year;
        $temporaryDisability->group_id                  = $request->group_id ?? $temporaryDisability->group_id;
        $temporaryDisability->gender                    = $request->gender ?? $temporaryDisability->gender;
        $temporaryDisability->is_outpatient             = $request->is_outpatient ?? $temporaryDisability->is_outpatient;
        $temporaryDisability->is_inpatient              = $request->is_inpatient ?? $temporaryDisability->is_inpatient;
        $temporaryDisability->one_day_unfit             = $request->one_day_unfit ?? $temporaryDisability->one_day_unfit;
        $temporaryDisability->two_days_unfit            = $request->two_days_unfit ?? $temporaryDisability->two_days_unfit;
        $temporaryDisability->three_days_unfit          = $request->three_days_unfit ?? $temporaryDisability->three_days_unfit;
        $temporaryDisability->four_days_unfit           = $request->four_days_unfit ?? $temporaryDisability->four_days_unfit;
        $temporaryDisability->five_or_more_days_unfit   = $request->five_or_more_days_unfit ?? $temporaryDisability->five_or_more_days_unfit;

        $result = $temporaryDisability->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $temporaryDisability]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.']);
        }
    }

    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $temporaryDisability = TemporaryDisabilityDaysBySector::find($id);

        if (!$temporaryDisability) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $result = $temporaryDisability->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.']);
        }
    }
}
