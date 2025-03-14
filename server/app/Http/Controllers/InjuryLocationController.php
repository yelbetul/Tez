<?php

namespace App\Http\Controllers;

use App\Models\InjuryLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class InjuryLocationController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'injury_location_code' => 'required|string|max:16|unique:injury_locations,injury_location_code',
                'group_code'          => 'required|string|max:3',
                'group_name'          => 'required|string|max:255',
                'sub_group_code'      => 'required|string|max:3',
                'sub_group_name'      => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'injury_location_code' => 'sometimes|string|max:16|unique:injury_locations,injury_location_code,' . $request->id,
                'group_code'          => 'sometimes|string|max:3',
                'group_name'          => 'sometimes|string|max:255',
                'sub_group_code'      => 'sometimes|string|max:3',
                'sub_group_name'      => 'sometimes|string|max:255',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }

    /**
     * Tüm kayıtları listele.
     */
    public function index()
    {
        $injuryLocations = InjuryLocation::all();
        return response()->json(['success' => true, 'data' => $injuryLocations]);
    }

    /**
     * Yeni kayıt oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) return $validation;

        $injuryLocation = new InjuryLocation();
        $injuryLocation->injury_location_code = $request->injury_location_code;
        $injuryLocation->group_code = $request->group_code;
        $injuryLocation->group_name = $request->group_name;
        $injuryLocation->sub_group_code = $request->sub_group_code;
        $injuryLocation->sub_group_name = $request->sub_group_name;

        $result = $injuryLocation->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla oluşturuldu.', 'data' => $injuryLocation]);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt oluşturulurken hata oluştu.']);
        }
    }

    /**
     * Kaydı güncelle.
     */
    public function update(Request $request, $id)
    {
        $injuryLocation = InjuryLocation::find($id);
        if (!$injuryLocation) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $injuryLocation->injury_location_code = $request->injury_location_code ?? $injuryLocation->injury_location_code;
        $injuryLocation->group_code = $request->group_code ?? $injuryLocation->group_code;
        $injuryLocation->group_name = $request->group_name ?? $injuryLocation->group_name;
        $injuryLocation->sub_group_code = $request->sub_group_code ?? $injuryLocation->sub_group_code;
        $injuryLocation->sub_group_name = $request->sub_group_name ?? $injuryLocation->sub_group_name;

        $result = $injuryLocation->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Kayıt başarıyla güncellendi.', 'data' => $injuryLocation]);
        } else {
            return response()->json(['success' => false, 'message' => 'Kayıt güncellenirken hata oluştu.']);
        }
    }

    /**
     * Kaydı sil.
     */
    public function destroy($id)
    {
        $injuryLocation = InjuryLocation::find($id);
        if (!$injuryLocation) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı!']);
        }

        $injuryLocation->delete();
        return response()->json(['success' => true, 'message' => 'Kayıt başarıyla silindi.']);
    }
}
