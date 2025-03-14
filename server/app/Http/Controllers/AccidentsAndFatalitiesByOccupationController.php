<?php

namespace App\Http\Controllers;

use App\Models\AccidentsAndFatalitiesByOccupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccidentsAndFatalitiesByOccupationController extends Controller
{
    /**
     * Validasyon Fonksiuonu
     */
    public function validateRequest($request, $type = 'store')
    {
        $rules = [];

        if ($type === 'store') {
            $rules = [
                'year'                      => 'required|string|max:4',
                'group_id'                  => 'required|exists:occupation_groups,id',
                'gender'                    => 'required|boolean',
                'works_on_accident_day'      => 'required|integer|min:0',
                'unfit_on_accident_day'      => 'required|integer|min:0',
                'two_days_unfit'            => 'required|integer|min:0',
                'three_days_unfit'          => 'required|integer|min:0',
                'four_days_unfit'           => 'required|integer|min:0',
                'five_or_more_days_unfit'   => 'required|integer|min:0',
                'fatalities'                => 'required|integer|min:0',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'year'                      => 'sometimes|string|max:4',
                'group_id'                  => 'sometimes|exists:occupation_groups,id',
                'gender'                    => 'sometimes|boolean',
                'works_on_accident_day'      => 'sometimes|integer|min:0',
                'unfit_on_accident_day'      => 'sometimes|integer|min:0',
                'two_days_unfit'            => 'sometimes|integer|min:0',
                'three_days_unfit'          => 'sometimes|integer|min:0',
                'four_days_unfit'           => 'sometimes|integer|min:0',
                'five_or_more_days_unfit'   => 'sometimes|integer|min:0',
                'fatalities'                => 'sometimes|integer|min:0',
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
        $data = AccidentsAndFatalitiesByOccupation::with('occupationGroup')->get();
        return response()->json($data);
    }

    /**
     * Yıla göre verileri listele.
     */
    public function indexByYear($year)
    {
        $data = AccidentsAndFatalitiesByOccupation::where('year', $year)->with('occupationGroup')->get();
        return response()->json($data);
    }

    /**
     * Grup ID'ye göre verileri listele.
     */
    public function indexByGroupId($groupId)
    {
        $data = AccidentsAndFatalitiesByOccupation::where('group_id', $groupId)->with('occupationGroup')->get();
        return response()->json($data);
    }

    /**
     * Cinsiyete göre verileri listele.
     */
    public function indexByGender($gender)
    {
        $data = AccidentsAndFatalitiesByOccupation::where('gender', $gender)->with('occupationGroup')->get();
        return response()->json($data);
    }
    /**
     * Code'a göre verileri listele.
     */
    public function indexByCode($code)
    {
        $data = AccidentsAndFatalitiesByOccupation::whereHas('occupationGroup', function ($query) use ($code) {
            $query->where('code', $code);
        })->with('occupationGroup')->get();

        return response()->json($data);
    }

    /**
     * Occupation Code'a göre verileri listele.
     */
    public function indexByOccupationCode($occupationCode)
    {
        $data = AccidentsAndFatalitiesByOccupation::whereHas('occupationGroup', function ($query) use ($occupationCode) {
            $query->where('occupation_code', $occupationCode);
        })->with('occupationGroup')->get();

        return response()->json($data);
    }

    /**
     * Group Code'a göre verileri listele.
     */
    public function indexByGroupCode($groupCode)
    {
        $data = AccidentsAndFatalitiesByOccupation::whereHas('occupationGroup', function ($query) use ($groupCode) {
            $query->where('group_code', $groupCode);
        })->with('occupationGroup')->get();

        return response()->json($data);
    }

    /**
     * Sub Group Code'a göre verileri listele.
     */
    public function indexBySubGroupCode($subGroupCode)
    {
        $data = AccidentsAndFatalitiesByOccupation::whereHas('occupationGroup', function ($query) use ($subGroupCode) {
            $query->where('sub_group_code', $subGroupCode);
        })->with('occupationGroup')->get();

        return response()->json($data);
    }

    /**
     * Pure Code'a göre verileri listele.
     */
    public function indexByPureCode($pureCode)
    {
        $data = AccidentsAndFatalitiesByOccupation::whereHas('occupationGroup', function ($query) use ($pureCode) {
            $query->where('pure_code', $pureCode);
        })->with('occupationGroup')->get();

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

        $record = new AccidentsAndFatalitiesByOccupation();
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

        $record = AccidentsAndFatalitiesByOccupation::find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.'], 404);
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
        $record = AccidentsAndFatalitiesByOccupation::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $record->delete();

        return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
    }
}
