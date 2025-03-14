<?php

namespace App\Http\Controllers;

use App\Models\SectorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class SectorCodeController extends Controller
{
    public function validateRequest($request, $type = 'store')
    {
        App::setLocale('tr');

        $rules = [];

        if ($type === 'store') {
            $rules = [
                'sector_code'    => 'required|string|max:16|unique:sector_codes,sector_code',
                'group_code'     => 'required|string|max:3',
                'group_name'     => 'required|string|max:255',
                'sub_group_code' => 'required|string|max:3',
                'sub_group_name' => 'required|string|max:255',
                'pure_code'      => 'required|string|max:3',
                'pure_name'      => 'required|string|max:255',
            ];
        } elseif ($type === 'update') {
            $rules = [
                'sector_code'    => 'sometimes|string|max:16|unique:sector_codes,sector_code,' . $request->id,
                'group_code'     => 'sometimes|string|max:3',
                'group_name'     => 'sometimes|string|max:255',
                'sub_group_code' => 'sometimes|string|max:3',
                'sub_group_name' => 'sometimes|string|max:255',
                'pure_code'      => 'sometimes|string|max:3',
                'pure_name'      => 'sometimes|string|max:255',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        return null;
    }
    /**
     * Tüm sektör kodlarını listele.
     */
    public function index()
    {
        $sectorCodes = SectorCode::all();
        return response()->json(['success' => true, 'data' => $sectorCodes]);
    }

    /**
     * Yeni sektör kodu oluştur.
     */
    public function store(Request $request)
    {

        $validation = $this->validateRequest($request, 'store');
        if ($validation) {
            return $validation;
        }

        $sectorCode = new SectorCode();
        $sectorCode->sector_code    = $request->sector_code;
        $sectorCode->group_code     = $request->group_code;
        $sectorCode->group_name     = $request->group_name;
        $sectorCode->sub_group_code = $request->sub_group_code;
        $sectorCode->sub_group_name = $request->sub_group_name;
        $sectorCode->pure_code      = $request->pure_code;
        $sectorCode->pure_name      = $request->pure_name;

        $result = $sectorCode->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Sektör kodu başarıyla oluşturuldu.', 'data' => $sectorCode]);
        } else {
            return response()->json(['success' => false, 'message' => 'Sektör kodu kaydedilirken hata oluştu.']);
        }
    }

    /**
     * Sektör kodunu güncelle.
     */
    public function update(Request $request, $id)
    {
        $sectorCode = SectorCode::find($id);
        if (!$sectorCode) {
            return response()->json(['success' => false, 'message' => 'Sektör kodu bulunamadı!']);
        }

        $validation = $this->validateRequest($request, 'update');
        if ($validation) return $validation;

        $sectorCode->sector_code    = $request->sector_code ?? $sectorCode->sector_code;
        $sectorCode->group_code     = $request->group_code ?? $sectorCode->group_code;
        $sectorCode->group_name     = $request->group_name ?? $sectorCode->group_name;
        $sectorCode->sub_group_code = $request->sub_group_code ?? $sectorCode->sub_group_code;
        $sectorCode->sub_group_name = $request->sub_group_name ?? $sectorCode->sub_group_name;
        $sectorCode->pure_code      = $request->pure_code ?? $sectorCode->pure_code;
        $sectorCode->pure_name      = $request->pure_name ?? $sectorCode->pure_name;

        $result = $sectorCode->save();

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Sektör kodu başarıyla güncellendi.', 'data' => $sectorCode]);
        } else {
            return response()->json(['success' => false, 'message' => 'Sektör kodu güncellenirken hata oluştu.']);
        }
    }

    /**
     * Sektör kodunu sil.
     */
    public function destroy($id)
    {
        $sectorCode = SectorCode::find($id);
        if (!$sectorCode) {
            return response()->json(['success' => false, 'message' => 'Sektör kodu bulunamadı!']);
        }

        $sectorCode->delete();
        return response()->json(['success' => true, 'message' => 'Sektör kodu başarıyla silindi.']);
    }
}
