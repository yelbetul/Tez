<?php

namespace App\Imports;

use App\Models\TemporaryDisabilityDaysByMonth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TemporaryDisabilityDaysByMonthImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'months' => 'required|integer|exists:months,id',
                'gender' => 'required|boolean',
                'one_day_unfit' => 'required|integer',
                'two_days_unfit' => 'required|integer',
                'three_days_unfit' => 'required|integer',
                'four_days_unfit' => 'required|integer',
                'five_or_more_days_unfit' => 'required|integer',
                'occupational_disease_cases' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'errors' => $validator->errors()->all(),
                ];
            }
        }

        if (!empty($this->errors)) {
            return;
        }

        foreach ($rows as $row) {
            TemporaryDisabilityDaysByMonth::create([
                'year' => $row['year'],
                'month_id' => $row['month_id'],
                'gender' => $row['gender'],
                'one_day_unfit' => $row['one_day_unfit'],
                'two_days_unfit' => $row['two_days_unfit'],
                'three_days_unfit' => $row['three_days_unfit'],
                'four_days_unfit' => $row['four_days_unfit'],
                'five_or_more_days_unfit' => $row['five_or_more_days_unfit'],
                'occupational_disease_cases' => $row['occupational_disease_cases'],
            ]);
        }
    }

    public function failures()
    {
        return $this->errors;
    }
}