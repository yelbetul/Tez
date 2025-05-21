<?php

    namespace App\Http\Controllers;

    use App\Models\TimeInterval;
    use App\Models\Admin;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Validator;

    class TimeIntervalController extends Controller
    {
        public function validateRequest($request, $type = 'store')
        {
            App::setLocale('tr');

            if($type === 'store'){
                $rules = [
                    'code' => 'required|string|max:16|unique:time_intervals,code',
                    'time_interval' => 'required|string|max:255',
                ];
            }elseif($type === 'update'){
                $rules = [
                    'code' => 'sometimes|string|max:16|unique:time_intervals,code,'.$request->id,
                    'time_interval' => 'sometimes|string|max:255',
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                return response()->json([
                                            'success' => false,
                                            'message' => $validator->errors()->first(),
                                        ]);
            }

            return null;
        }

        private function authenticate(Request $request)
        {
            $admin_id = $request->header('X-ADMIN-ID');
            $api_key = $request->header('X-API-KEY');
            $secret_key = $request->header('X-SECRET-KEY');

            if(!$admin_id || !$api_key || !$secret_key){
                return response()->json([
                                            'success' => false,
                                            'message' => 'Kimlik doğrulama bilgileri eksik!',
                                        ]);
            }

            $admin = Admin::where('id', $admin_id)
                ->where('api_key', $api_key)
                ->where('secret_key', $secret_key)
                ->first();

            if(!$admin){
                return response()->json([
                                            'success' => false,
                                            'message' => 'Yetkiniz bulunmamaktadır!',
                                        ]);
            }

            return $admin;
        }

        public function index()
        {
            $timeIntervals = TimeInterval::all();

            if($timeIntervals->isEmpty()){
                return response()->json([
                                            'success' => false,
                                            'message' => 'Zaman aralığı kaydı bulunamadı.',
                                        ]);
            }

            return response()->json([
                                        'success' => true,
                                        'data' => $timeIntervals,
                                    ]);
        }

        public function indexUser()
        {
            return DB::table('time_intervals')
                ->select(
                    'code',
                    'time_interval'
                )
                ->orderBy('code')
                ->get()
                ->map(function($item){
                    return [
                        'code' => $item->code,
                        'time_interval' => $item->time_interval,
                    ];
                });
        }

        public function store(Request $request)
        {
            $auth = $this->authenticate($request);
            if($auth instanceof \Illuminate\Http\JsonResponse)
                return $auth;

            $validation = $this->validateRequest($request, 'store');
            if($validation){
                return $validation;
            }

            $timeInterval = new TimeInterval();
            $timeInterval->code = $request->code;
            $timeInterval->time_interval = $request->time_interval;

            $result = $timeInterval->save();

            if($result){
                return response()->json([
                                            'success' => true,
                                            'message' => 'Zaman aralığı kaydı başarıyla oluşturuldu.',
                                            'data' => $timeInterval,
                                        ]);
            }else{
                return response()->json([
                                            'success' => false,
                                            'message' => 'Zaman aralığı kaydı oluşturulurken hata oluştu.',
                                        ]);
            }
        }

        public function update(Request $request, $id)
        {
            $auth = $this->authenticate($request);
            if($auth instanceof \Illuminate\Http\JsonResponse)
                return $auth;

            $timeInterval = TimeInterval::find($id);
            if(!$timeInterval){
                return response()->json([
                                            'success' => false,
                                            'message' => 'Zaman aralığı kaydı bulunamadı!',
                                        ]);
            }

            $validation = $this->validateRequest($request, 'update');
            if($validation){
                return $validation;
            }

            $timeInterval->code = $request->code ?? $timeInterval->code;
            $timeInterval->time_interval = $request->time_interval ?? $timeInterval->time_interval;

            $result = $timeInterval->save();

            if($result){
                return response()->json([
                                            'success' => true,
                                            'message' => 'Zaman aralığı kaydı başarıyla güncellendi.',
                                            'data' => $timeInterval,
                                        ]);
            }else{
                return response()->json([
                                            'success' => false,
                                            'message' => 'Zaman aralığı kaydı güncellenirken hata oluştu.',
                                        ]);
            }
        }

        public function destroy(Request $request, $id)
        {
            $auth = $this->authenticate($request);
            if($auth instanceof \Illuminate\Http\JsonResponse)
                return $auth;

            $timeInterval = TimeInterval::find($id);
            if(!$timeInterval){
                return response()->json([
                                            'success' => false,
                                            'message' => 'Zaman aralığı kaydı bulunamadı!',
                                        ]);
            }

            if($timeInterval->delete()){
                return response()->json([
                                            'success' => true,
                                            'message' => 'Zaman aralığı kaydı başarıyla silindi.',
                                        ]);
            }

            return response()->json([
                                        'success' => false,
                                        'message' => 'Zaman aralığı kaydı silinirken hata oluştu.',
                                    ]);
        }
    }
