<?php

namespace App\Http\Controllers;

use App\Models\EmployeeEmploymentDuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class EmployeeEmploymentDurationController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'code'                => 'required|string|max:16|unique:employee_employment_durations,code',
                'employment_duration' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code'                => 'sometimes|string|max:16|unique:employee_employment_durations,code,' . $request->id,
                'employment_duration' => 'sometimes|string|max:255',
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
     * Tüm çalışan istihdam süresi kayıtlarını listele.
     */
    public function index()
    {
        $durations = EmployeeEmploymentDuration::all();
        return response()->json([
            'success' => true,
            'data'    => $durations
        ]);
    }

    /**
     * Yeni çalışan istihdam süresi kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $duration = new EmployeeEmploymentDuration();
        $duration->code = $request->code;
        $duration->employment_duration = $request->employment_duration;

        $result = $duration->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan istihdam süresi kaydı başarıyla oluşturuldu.',
                'data'    => $duration
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Çalışan istihdam süresi kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $duration = EmployeeEmploymentDuration::find($id);
        if (!$duration) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $duration->code = $request->code ?? $duration->code;
        $duration->employment_duration = $request->employment_duration ?? $duration->employment_duration;

        $result = $duration->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan istihdam süresi kaydı başarıyla güncellendi.',
                'data'    => $duration
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Çalışan istihdam süresi kaydını sil.
     */
    public function destroy($id)
    {
        $duration = EmployeeEmploymentDuration::find($id);
        if (!$duration) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan istihdam süresi kaydı bulunamadı!'
            ]);
        }

        $duration->delete();
        return response()->json([
            'success' => true,
            'message' => 'Çalışan istihdam süresi kaydı başarıyla silindi.'
        ]);
    }
}
