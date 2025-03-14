<?php

namespace App\Http\Controllers;

use App\Models\WorkEnvironment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class WorkEnvironmentController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'environment_code' => 'required|string|max:16|unique:work_environments,environment_code',
                'group_code'       => 'required|string|max:3',
                'group_name'       => 'required|string|max:255',
                'sub_group_code'   => 'required|string|max:3',
                'sub_group_name'   => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'environment_code' => 'sometimes|string|max:16|unique:work_environments,environment_code,' . $request->id,
                'group_code'       => 'sometimes|string|max:3',
                'group_name'       => 'sometimes|string|max:255',
                'sub_group_code'   => 'sometimes|string|max:3',
                'sub_group_name'   => 'sometimes|string|max:255',
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
     * Tüm iş ortamı kayıtlarını listele.
     */
    public function index()
    {
        $workEnvironments = WorkEnvironment::all();
        return response()->json([
            'success' => true,
            'data'    => $workEnvironments
        ]);
    }

    /**
     * Yeni iş ortamı kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $workEnvironment = new WorkEnvironment();
        $workEnvironment->environment_code = $request->environment_code;
        $workEnvironment->group_code       = $request->group_code;
        $workEnvironment->group_name       = $request->group_name;
        $workEnvironment->sub_group_code   = $request->sub_group_code;
        $workEnvironment->sub_group_name   = $request->sub_group_name;

        $result = $workEnvironment->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş ortamı kaydı başarıyla oluşturuldu.',
                'data'    => $workEnvironment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı oluşturulurken hata oluştu.'
            ]);
        }
    }

    /**
     * İş ortamı kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $workEnvironment = WorkEnvironment::find($id);
        if (!$workEnvironment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $workEnvironment->environment_code = $request->environment_code ?? $workEnvironment->environment_code;
        $workEnvironment->group_code       = $request->group_code ?? $workEnvironment->group_code;
        $workEnvironment->group_name       = $request->group_name ?? $workEnvironment->group_name;
        $workEnvironment->sub_group_code   = $request->sub_group_code ?? $workEnvironment->sub_group_code;
        $workEnvironment->sub_group_name   = $request->sub_group_name ?? $workEnvironment->sub_group_name;

        $result = $workEnvironment->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'İş ortamı kaydı başarıyla güncellendi.',
                'data'    => $workEnvironment
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * İş ortamı kaydını sil.
     */
    public function destroy($id)
    {
        $workEnvironment = WorkEnvironment::find($id);
        if (!$workEnvironment) {
            return response()->json([
                'success' => false,
                'message' => 'İş ortamı kaydı bulunamadı!'
            ]);
        }

        $workEnvironment->delete();
        return response()->json([
            'success' => true,
            'message' => 'İş ortamı kaydı başarıyla silindi.'
        ]);
    }
}
