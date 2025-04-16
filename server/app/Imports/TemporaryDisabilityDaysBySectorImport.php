<?php

namespace App\Imports;

use App\Models\TemporaryDisabilityDaysBySector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class TemporaryDisabilityDaysBySectorImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'group_id' => 'required|integer|exists:sector_codes,id',
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
                    'row' => $index + 2, // 1. satır başlık, o yüzden +2
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }
        }

        // Hatalar varsa hiçbir veriyi ekleme
        if (!empty($this->errors)) {
            return;
        }

        // Hata yoksa verileri ekle
        foreach ($rows as $row) {
            TemporaryDisabilityDaysBySector::create([
                'year' => $row['year'],
                'group_id' => $row['group_id'],
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