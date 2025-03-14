<?php

namespace App\Http\Controllers;

use App\Models\WorkstationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class WorkstationTypeController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'workstation_code' => 'required|string|max:16|unique:workstation_types,workstation_code',
                'workstation_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'workstation_code' => 'sometimes|string|max:16|unique:workstation_types,workstation_code,' . $request->id,
                'workstation_name' => 'sometimes|string|max:255',
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
     * Tüm istasyon tipi kayıtlarını listele.
     */
    public function index()
    {
        $workstationTypes = WorkstationType::all();
        return response()->json([
            'success' => true,
            'data'    => $workstationTypes
        ]);
    }

    /**
     * Yeni istasyon tipi kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }
        $workstationType = new WorkstationType();
        $workstationType->workstation_code = $request->workstation_code;
        $workstationType->workstation_name = $request->workstation_name;

        $result = $workstationType->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İstasyon tipi kaydı başarıyla oluşturuldu.',
                'data'    => $workstationType
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * İstasyon tipi kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $workstationType = WorkstationType::find($id);
        if (!$workstationType) {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $workstationType->workstation_code = $request->workstation_code ?? $workstationType->workstation_code;
        $workstationType->workstation_name = $request->workstation_name ?? $workstationType->workstation_name;

        $result = $workstationType->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İstasyon tipi kaydı başarıyla güncellendi.',
                'data'    => $workstationType
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * İstasyon tipi kaydını sil.
     */
    public function destroy($id)
    {
        $workstationType = WorkstationType::find($id);
        if (!$workstationType) {
            return response()->json([
                'success' => false,
                'message' => 'İstasyon tipi kaydı bulunamadı!'
            ]);
        }

        $workstationType->delete();
        return response()->json([
            'success' => true,
            'message' => 'İstasyon tipi kaydı başarıyla silindi.'
        ]);
    }
}
