<?php

namespace App\Imports;

use App\Models\WorkAccidentsByMonth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class WorkAccidentsByMonthImport implements ToCollection, WithHeadingRow
{
    public $failures = [];

    public function collection(Collection $rows)
    {
        $dataToInsert = [];

        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'month_id' => 'required|integer|exists:months,id',
                'gender' => 'required|boolean',
                'works_on_accident_day' => 'required|integer',
                'unfit_on_accident_day' => 'required|integer',
                'two_days_unfit' => 'required|integer',
                'three_days_unfit' => 'required|integer',
                'four_days_unfit' => 'required|integer',
                'five_or_more_days_unfit' => 'required|integer',
                'occupational_disease_cases' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $this->failures[] = [
                    'row' => $index + 2, // heading row +1
                    'errors' => $validator->errors()->all()
                ];
            } else {
                $dataToInsert[] = [
                    'year' => $row['year'],
                    'month_id' => $row['month_id'],
                    'gender' => $row['gender'],
                    'works_on_accident_day' => $row['works_on_accident_day'],
                    'unfit_on_accident_day' => $row['unfit_on_accident_day'],
                    'two_days_unfit' => $row['two_days_unfit'],
                    'three_days_unfit' => $row['three_days_unfit'],
                    'four_days_unfit' => $row['four_days_unfit'],
                    'five_or_more_days_unfit' => $row['five_or_more_days_unfit'],
                    'occupational_disease_cases' => $row['occupational_disease_cases'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Eğer hiç hata yoksa topluca veritabanına yaz
        if (empty($this->failures)) {
            WorkAccidentsByMonth::insert($dataToInsert);
        }
    }

    public function failures()
    {
        return $this->failures;
    }
}