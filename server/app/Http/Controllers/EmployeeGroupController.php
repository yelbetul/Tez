<?php

namespace App\Http\Controllers;

use App\Models\EmployeeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class EmployeeGroupController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        if ($type === 'store') {
            $rules = [
                'code'           => 'required|string|max:16|unique:employee_groups,code',
                'employee_count' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'code'           => 'sometimes|string|max:16|unique:employee_groups,code,' . $request->id,
                'employee_count' => 'sometimes|string|max:255',
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
     * Tüm çalışan grubu kayıtlarını listele.
     */
    public function index()
    {
        $employeeGroups = EmployeeGroup::all();
        return response()->json([
            'success' => true,
            'data'    => $employeeGroups
        ]);
    }

    /**
     * Yeni çalışan grubu kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $employeeGroup = new EmployeeGroup();
        $employeeGroup->code = $request->code;
        $employeeGroup->employee_count = $request->employee_count;

        $result = $employeeGroup->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan grubu kaydı başarıyla oluşturuldu.',
                'data'    => $employeeGroup
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * Çalışan grubu kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $employeeGroup = EmployeeGroup::find($id);
        if (!$employeeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $employeeGroup->code = $request->code ?? $employeeGroup->code;
        $employeeGroup->employee_count = $request->employee_count ?? $employeeGroup->employee_count;

        $result = $employeeGroup->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Çalışan grubu kaydı başarıyla güncellendi.',
                'data'    => $employeeGroup
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Çalışan grubu kaydını sil.
     */
    public function destroy($id)
    {
        $employeeGroup = EmployeeGroup::find($id);
        if (!$employeeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Çalışan grubu bulunamadı!'
            ]);
        }

        $employeeGroup->delete();
        return response()->json([
            'success' => true,
            'message' => 'Çalışan grubu kaydı başarıyla silindi.'
        ]);
    }
}
