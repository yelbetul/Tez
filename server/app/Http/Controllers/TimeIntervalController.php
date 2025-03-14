<?php

namespace App\Http\Controllers;

use App\Models\TimeInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class TimeIntervalController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'code'          => 'required|string|max:16|unique:time_intervals,code',
                'time_interval' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code'          => 'sometimes|string|max:16|unique:time_intervals,code,' . $request->id,
                'time_interval' => 'sometimes|string|max:255',
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
     * Tüm kayıtları listele.
     */
    public function index()
    {
        $timeIntervals = TimeInterval::all();
        return response()->json([
            'success' => true,
            'data'    => $timeIntervals
        ]);
    }

    /**
     * Yeni zaman aralığı kaydı oluştur.
     */
    public function store(Request $request)
    {
        // Validasyon işlemi
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        // Yeni kayıt oluşturma
        $timeInterval = new TimeInterval();
        $timeInterval->code = $request->code;
        $timeInterval->time_interval = $request->time_interval;

        // Kaydetme işlemi
        $result = $timeInterval->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Zaman aralığı kaydı başarıyla oluşturuldu.',
                'data'    => $timeInterval
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Zaman aralığı kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Zaman aralığı kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        // İlgili kaydı bul
        $timeInterval = TimeInterval::find($id);
        if (!$timeInterval) {
            return response()->json([
                'success' => false,
                'message' => 'Zaman aralığı kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $timeInterval->code = $request->code ?? $timeInterval->code;
        $timeInterval->time_interval = $request->time_interval ?? $timeInterval->time_interval;

        $result = $timeInterval->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Zaman aralığı kaydı başarıyla güncellendi.',
                'data'    => $timeInterval
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Zaman aralığı kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Zaman aralığı kaydını sil.
     */
    public function destroy($id)
    {
        $timeInterval = TimeInterval::find($id);
        if (!$timeInterval) {
            return response()->json([
                'success' => false,
                'message' => 'Zaman aralığı kaydı bulunamadı!'
            ]);
        }

        $timeInterval->delete();
        return response()->json([
            'success' => true,
            'message' => 'Zaman aralığı kaydı başarıyla silindi.'
        ]);
    }
}
