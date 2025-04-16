<?php

namespace App\Imports;

use App\Models\FatalWorkAccidentsBySector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FatalWorkAccidentsBySectorImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'year' => 'required',
                'group_id' => 'required|integer|exists:sector_codes,id',
                'gender' => 'required|boolean',
                'work_accident_fatalities' => 'required|integer',
                'occupational_disease_fatalities' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2, // Başlık satırı var, bu yüzden +2
                    'errors' => $validator->errors()->all(),
                ];
            }
        }

        if (!empty($this->errors)) {
            return;
        }

        foreach ($rows as $row) {
            FatalWorkAccidentsBySector::create([
                'year' => $row['year'],
                'group_id' => $row['group_id'],
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