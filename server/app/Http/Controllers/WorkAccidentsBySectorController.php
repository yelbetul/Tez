<?php

namespace App\Http\Controllers;

use App\Models\WorkAccidentsBySector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class WorkAccidentsBySectorController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year' => 'required|string|max:4',
                'group_id' => 'required|exists:sector_codes,id',
                'gender' => 'required|boolean',
                'works_on_accident_day' => 'required|integer|min:0',
                'unfit_on_accident_day' => 'required|integer|min:0',
                'two_days_unfit' => 'required|integer|min:0',
                'three_days_unfit' => 'required|integer|min:0',
                'four_days_unfit' => 'required|integer|min:0',
                'five_or_more_days_unfit' => 'required|integer|min:0',
                'occupational_disease_cases' => 'required|integer|min:0|max:127',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year' => 'sometimes|string|max:4',
                'group_id' => 'sometimes|exists:sector_codes,id',
                'gender' => 'sometimes|boolean',
                'works_on_accident_day' => 'sometimes|integer|min:0',
                'unfit_on_accident_day' => 'sometimes|integer|min:0',
                'two_days_unfit' => 'sometimes|integer|min:0',
                'three_days_unfit' => 'sometimes|integer|min:0',
                'four_days_unfit' => 'sometimes|integer|min:0',
                'five_or_more_days_unfit' => 'sometimes|integer|min:0',
                'occupational_disease_cases' => 'sometimes|integer|min:0|max:127',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

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
        $data = WorkAccidentsBySector::all();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = WorkAccidentsBySector::where('year', $year)->get();
        return response()->json($data);
    }
    /**
     * Cinsiyete göre verileri listele.
     */
    public function indexByGender($gender)
    {
        $data = WorkAccidentsBySector::where('gender', $gender)->get();
        return response()->json($data);
    }
    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = WorkAccidentsBySector::where('group_id', $groupId)->get();
        return response()->json($data);
    }

    /**
     * Sector code'a göre verileri listele.
     */
    public function indexBySectorCode($sectorCode)
    {
        $data = WorkAccidentsBySector::whereHas('sector', function ($query) use ($sectorCode) {
            $query->where('sector_code', $sectorCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Group code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = WorkAccidentsBySector::whereHas('sector', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Sub-group code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = WorkAccidentsBySector::whereHas('sector', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->get();
        return response()->json($data);
    }

    /**
     * Pure code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = WorkAccidentsBySector::whereHas('sector', function ($query) use ($pureCode) {
            $query->where('pure_code', $pureCode);
        })->get();
        return response()->json($data);
    }


    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $workAccident = new WorkAccidentsBySector();
        $workAccident->year                      = $request->year;
        $workAccident->group_id                  = $request->group_id;
        $workAccident->gender                    = $request->gender;
        $workAccident->works_on_accident_day     = $request->works_on_accident_day;
        $workAccident->unfit_on_accident_day     = $request->unfit_on_accident_day;
        $workAccident->two_days_unfit            = $request->two_days_unfit;
        $workAccident->three_days_unfit          = $request->three_days_unfit;
        $workAccident->four_days_unfit           = $request->four_days_unfit;
        $workAccident->five_or_more_days_unfit   = $request->five_or_more_days_unfit;
        $workAccident->occupational_disease_cases = $request->occupational_disease_cases;

        $result = $workAccident->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla kaydedildi.', 'data' => $workAccident]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri kaydedilirken hata oluştu.']);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $workAccident = WorkAccidentsBySector::findOrFail($id);

        $workAccident->year                      = $request->year ?? $workAccident->year;
        $workAccident->group_id                  = $request->group_id ?? $workAccident->group_id;
        $workAccident->gender                    = $request->gender ?? $workAccident->gender;
        $workAccident->works_on_accident_day     = $request->works_on_accident_day ?? $workAccident->works_on_accident_day;
        $workAccident->unfit_on_accident_day     = $request->unfit_on_accident_day ?? $workAccident->unfit_on_accident_day;
        $workAccident->two_days_unfit            = $request->two_days_unfit ?? $workAccident->two_days_unfit;
        $workAccident->three_days_unfit          = $request->three_days_unfit ?? $workAccident->three_days_unfit;
        $workAccident->four_days_unfit           = $request->four_days_unfit ?? $workAccident->four_days_unfit;
        $workAccident->five_or_more_days_unfit   = $request->five_or_more_days_unfit ?? $workAccident->five_or_more_days_unfit;
        $workAccident->occupational_disease_cases = $request->occupational_disease_cases ?? $workAccident->occupational_disease_cases;

        $result = $workAccident->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $workAccident]);
        } else {
            return response()->json(['success' => false, 'message' => 'Veri güncellenirken hata oluştu.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $workAccident = WorkAccidentsBySector::find($id);

        if (!$workAccident) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $result = $workAccident->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt silinirken hata oluştu.']);
        }
    }
}
