<?php

namespace App\Imports;

use App\Models\TemporaryDisabilityDaysByProvince;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TemporaryDisabilityDaysByProvinceImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'province_id' => 'required|integer|exists:province_codes,id',
                'gender' => 'required|boolean',
                'is_outpatient' => 'required|boolean',
                'one_day_unfit' => 'required|integer',
                'two_days_unfit' => 'required|integer',
                'three_days_unfit' => 'required|integer',
                'four_days_unfit' => 'required|integer',
                'five_or_more_days_unfit' => 'required|integer',
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
            TemporaryDisabilityDaysByProvince::create([
                'year' => $row['year'],
                'province_id' => $row['province_id'],
                'gender' => $row['gender'],
                'is_outpatient' => $row['is_outpatient'],
                'one_day_unfit' => $row['one_day_unfit'],
                'two_days_unfit' => $row['two_days_unfit'],
                'three_days_unfit' => $row['three_days_unfit'],
                'four_days_unfit' => $row['four_days_unfit'],
                'five_or_more_days_unfit' => $row['five_or_more_days_unfit'],
            ]);
        }
    }

    public function failures()
    {
        return $this->errors;
    }
}