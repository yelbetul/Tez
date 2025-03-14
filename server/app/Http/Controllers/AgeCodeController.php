<?php

namespace App\Http\Controllers;

use App\Models\AgeCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class AgeCodeController extends Controller
{
    // Validasyon fonksiyonu
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'age' => 'required|integer|min:1|max:150|unique:age_codes,age',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'age' => 'sometimes|integer|min:1|max:150|unique:age_codes,age,' . $request->id,
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ageCodes = AgeCode::all();
        return response()->json(['success' => true, 'data' => $ageCodes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $ageCode = new AgeCode();
        $ageCode->age = $request->age;

        $result = $ageCode->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Yaş kodu başarıyla oluşturuldu.', 'data' => $ageCode]);
        } else {
            return response()->json(['success' => false, 'message' => 'Yaş kodu oluşturulurken bir hata oluştu.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ageCode = AgeCode::find($id);
        if (!$ageCode) {
            return response()->json(['success' => false, 'message' => 'Yaş kodu bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $ageCode->age = $request->age;

        $result = $ageCode->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Yaş kodu başarıyla güncellendi.', 'data' => $ageCode]);
        } else {
            return response()->json(['success' => false, 'message' => 'Yaş kodu güncellenirken bir hata oluştu.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ageCode = AgeCode::find($id);
        if (!$ageCode) {
            return response()->json(['success' => false, 'message' => 'Yaş kodu bulunamadı!']);
        }

        $result = $ageCode->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Yaş kodu başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Yaş kodu silinirken bir hata oluştu.']);
        }
    }
}
