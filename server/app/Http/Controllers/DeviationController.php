<?php

namespace App\Http\Controllers;

use App\Models\Deviation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class DeviationController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'deviation_code' => 'required|string|max:16|unique:deviations,deviation_code',
                'group_code'     => 'required|string|max:3',
                'group_name'     => 'required|string|max:255',
                'sub_group_code' => 'required|string|max:3',
                'sub_group_name' => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'deviation_code' => 'sometimes|string|max:16|unique:deviations,deviation_code,' . $request->id,
                'group_code'     => 'sometimes|string|max:3',
                'group_name'     => 'sometimes|string|max:255',
                'sub_group_code' => 'sometimes|string|max:3',
                'sub_group_name' => 'sometimes|string|max:255',
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
     * Tüm sapma (deviation) kayıtlarını listele.
     */
    public function index()
    {
        $deviations = Deviation::all();
        return response()->json([
            'success' => true,
            'data'    => $deviations
        ]);
    }

    /**
     * Yeni sapma (deviation) kaydı oluştur.
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $deviation = new Deviation();
        $deviation->deviation_code = $request->deviation_code;
        $deviation->group_code     = $request->group_code;
        $deviation->group_name     = $request->group_name;
        $deviation->sub_group_code = $request->sub_group_code;
        $deviation->sub_group_name = $request->sub_group_name;

        $result = $deviation->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Sapma kaydı başarıyla oluşturuldu.',
                'data'    => $deviation
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı kaydedilirken hata oluştu.'
            ]);
        }
    }

    /**
     * Sapma kaydını güncelle.
     */
    public function update(Request $request, $id)
    {
        $deviation = Deviation::find($id);
        if (!$deviation) {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı bulunamadı!'
            ]);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) {
            return $validation;
        }

        $deviation->deviation_code = $request->deviation_code ?? $deviation->deviation_code;
        $deviation->group_code     = $request->group_code ?? $deviation->group_code;
        $deviation->group_name     = $request->group_name ?? $deviation->group_name;
        $deviation->sub_group_code = $request->sub_group_code ?? $deviation->sub_group_code;
        $deviation->sub_group_name = $request->sub_group_name ?? $deviation->sub_group_name;

        $result = $deviation->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Sapma kaydı başarıyla güncellendi.',
                'data'    => $deviation
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı güncellenirken hata oluştu.'
            ]);
        }
    }

    /**
     * Sapma kaydını sil.
     */
    public function destroy($id)
    {
        $deviation = Deviation::find($id);
        if (!$deviation) {
            return response()->json([
                'success' => false,
                'message' => 'Sapma kaydı bulunamadı!'
            ]);
        }

        $deviation->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sapma kaydı başarıyla silindi.'
        ]);
    }
}
