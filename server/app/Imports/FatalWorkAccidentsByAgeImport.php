<?php

namespace App\Imports;

use App\Models\FatalWorkAccidentsByAge;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FatalWorkAccidentsByAgeImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'age_id' => 'required|integer|exists:age_codes,id',
                'gender' => 'required|boolean',
                'work_accident_fatalities' => 'required|integer',
                'occupational_disease_fatalities' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2, // +2 çünkü 1. satır başlık
                    'errors' => $validator->errors()->all(),
                ];
            }
        }

        if (!empty($this->errors)) {
            return;
        }

        foreach ($rows as $row) {
            FatalWorkAccidentsByAge::create([
                'year' => $row['year'],
                'age_id' => $row['age_id'],
                'gender' => $row['gender'],
                'work_accident_fatalities' => $row['work_accident_fatalities'],
                'occupational_disease_fatalities' => $row['occupational_disease_fatalities'],
            ]);
        }
    }

    public function failures()
    {
        return $this->errors;
    }
}