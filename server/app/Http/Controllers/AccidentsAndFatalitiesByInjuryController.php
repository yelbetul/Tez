<?php

namespace App\Http\Controllers;

use App\Models\AccidentsAndFatalitiesByInjury;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccidentsAndFatalitiesByInjuryController extends Controller
{
    /**
     * Validasyon
     */
    public function validateRequest($request, $type = 'store')
    {
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year'                     => 'required|string|max:4',
                'group_id'                 => 'required|exists:injury_types,id',
                'gender'                   => 'required|boolean',
                'works_on_accident_day'     => 'required|integer|min:0',
                'unfit_on_accident_day'     => 'required|integer|min:0',
                'two_days_unfit'           => 'required|integer|min:0',
                'three_days_unfit'         => 'required|integer|min:0',
                'four_days_unfit'          => 'required|integer|min:0',
                'five_or_more_days_unfit'  => 'required|integer|min:0',
                'fatalities'               => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                     => 'sometimes|string|max:4',
                'group_id'                 => 'sometimes|exists:injury_types,id',
                'gender'                   => 'sometimes|boolean',
                'works_on_accident_day'     => 'sometimes|integer|min:0',
                'unfit_on_accident_day'     => 'sometimes|integer|min:0',
                'two_days_unfit'           => 'sometimes|integer|min:0',
                'three_days_unfit'         => 'sometimes|integer|min:0',
                'four_days_unfit'          => 'sometimes|integer|min:0',
                'five_or_more_days_unfit'  => 'sometimes|integer|min:0',
                'fatalities'               => 'sometimes|integer|min:0',
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
        $data = AccidentsAndFatalitiesByInjury::with('injuryType')->get();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = AccidentsAndFatalitiesByInjury::where('year', $year)->with('injuryType')->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = AccidentsAndFatalitiesByInjury::where('group_id', $groupId)->with('injuryType')->get();
        return response()->json($data);
    }

    /**
     * Cinsiyete göre verileri listele.
     */
    public function indexByGender($gender)
    {
        $data = AccidentsAndFatalitiesByInjury::where('gender', $gender)->with('injuryType')->get();
        return response()->json($data);
    }

    /**
     * Injury Code'a göre verileri listele.
     */
    public function indexByInjuryCode($injuryCode)
    {
        $data = AccidentsAndFatalitiesByInjury::whereHas('injuryType', function ($query) use ($injuryCode) {
            $query->where('injury_code', $injuryCode);
        })->with('injuryType')->get();

        return response()->json($data);
    }

    /**
     * Group Code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = AccidentsAndFatalitiesByInjury::whereHas('injuryType', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->with('injuryType')->get();

        return response()->json($data);
    }

    /**
     * Sub Group Code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = AccidentsAndFatalitiesByInjury::whereHas('injuryType', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->with('injuryType')->get();

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

        $record = new AccidentsAndFatalitiesByInjury();
        $record->year = $request->year;
        $record->group_id = $request->group_id;
        $record->gender = $request->gender;
        $record->works_on_accident_day = $request->works_on_accident_day;
        $record->unfit_on_accident_day = $request->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit;
        $record->fatalities = $request->fatalities;

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

        $record = AccidentsAndFatalitiesByInjury::find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->year = $request->year ?? $record->year;
        $record->group_id = $request->group_id ?? $record->group_id;
        $record->gender = $request->gender ?? $record->gender;
        $record->works_on_accident_day = $request->works_on_accident_day ?? $record->works_on_accident_day;
        $record->unfit_on_accident_day = $request->unfit_on_accident_day ?? $record->unfit_on_accident_day;
        $record->two_days_unfit = $request->two_days_unfit ?? $record->two_days_unfit;
        $record->three_days_unfit = $request->three_days_unfit ?? $record->three_days_unfit;
        $record->four_days_unfit = $request->four_days_unfit ?? $record->four_days_unfit;
        $record->five_or_more_days_unfit = $request->five_or_more_days_unfit ?? $record->five_or_more_days_unfit;
        $record->fatalities = $request->fatalities ?? $record->fatalities;

        $result = $record->save();

        return response()->json(['success' => true, 'message' => 'Veri başarıyla güncellendi.', 'data' => $record]);
    }

    /**
     * Kayıt silme (DESTROY).
     */
    public function destroy($id)
    {
        $record = AccidentsAndFatalitiesByInjury::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->delete();

        return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
    }
}
