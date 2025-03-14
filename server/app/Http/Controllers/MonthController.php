<?php

namespace App\Http\Controllers;

use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class MonthController extends Controller
{
    private function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];
        if ($type === 'store') {
            $rules = [
                'month_name' => 'required|string|max:255|unique:months,month_name',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'month_name' => 'sometimes|string|max:255|unique:months,month_name,' . $request->id,
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }
    public function index()
    {
        $months = Month::all();
        return response()->json(['success' => true, 'data' => $months]);
    }

    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $month = new Month();
        $month->month_name = $request->month_name;
        $result = $month->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Ay başarıyla kaydedildi.', 'data' => $month]);
        } else {
            return response()->json(['success' => false, 'message' => 'Ay kaydedilirken bir hata oluştu.']);
        }
    }

    public function update(Request $request, $id)
    {

        $month = Month::find($id);
        if (!$month) {
            return response()->json(['success' => false, 'message' => 'Ay bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $month->month_name = $request->month_name;
        $result = $month->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Ay başarıyla güncellendi.', 'data' => $month]);
        } else {
            return response()->json(['success' => false, 'message' => 'Ay güncellenirken bir hata oluştu.']);
        }
    }

    public function destroy($id)
    {
        $month = Month::find($id);
        if (!$month) {
            return response()->json(['success' => false, 'message' => 'Ay bulunamadı!']);
        }

        $result = $month->delete();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Ay başarıyla silindi.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Ay silinirken bir hata oluştu.']);
        }
    }
}
