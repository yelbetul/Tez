<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class OccupationStatsController extends Controller
    {
        /**
         * Örnek: GET /api/occupations/summary/2023
         */
        public function summary($year)
        {
            // 1) Kaza verileri
            $accQ = DB::table('accidents_and_fatalities_by_occupations')
                ->where('year', $year);

            // Toplam iş kazası (tüm vakalar + ölümler)
            $totalAccidents = $accQ->sum(DB::raw(
                'works_on_accident_day
             + unfit_on_accident_day
             + two_days_unfit
             + three_days_unfit
             + four_days_unfit
             + five_or_more_days_unfit
             + fatalities'
            ));

            // Ölümlü iş kazası
            $totalFatalAccidents = $accQ->sum('fatalities');

            // 2) Meslek hastalığı verileri
            $disQ = DB::table('occ_disease_fatalities_by_occupations')
                ->where('year', $year);

            // Meslek hastalığına yakalanan toplam
            $totalDiseases = $disQ->sum('occ_disease_cases');

            // 3) Ortalama iş göremezlik süresi (gün)
            //    ağırlıklı gün sayısı
            $weightedDays = $accQ->sum(DB::raw(
                'unfit_on_accident_day * 1
             + two_days_unfit       * 2
             + three_days_unfit     * 3
             + four_days_unfit      * 4
             + five_or_more_days_unfit * 5'
            ));

            // toplam iş göremez vaka sayısı
            $totalUnfitCases = $accQ->sum(DB::raw(
                'unfit_on_accident_day
             + two_days_unfit
             + three_days_unfit
             + four_days_unfit
             + five_or_more_days_unfit'
            ));

            $avgUnfitDays = $totalUnfitCases > 0
                ? round($weightedDays / $totalUnfitCases, 1)
                : 0;

            return response()->json([
                                        'year'                 => $year,
                                        'total_accidents'      => $totalAccidents,
                                        'total_fatal_accidents'=> $totalFatalAccidents,
                                        'total_diseases'       => $totalDiseases,
                                        'avg_unfit_days'       => $avgUnfitDays,
                                    ]);
        }
    }
